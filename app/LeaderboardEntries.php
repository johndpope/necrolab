<?php

namespace App;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use App\Traits\IsSchemaTable;
use App\Traits\HasPartitions;
use App\Traits\HasTempTable;
use App\Traits\HasCompositePrimaryKey;
use App\Dates;
use App\ExternalSites;
use App\LeaderboardSources;
use App\Leaderboards;
use App\LeaderboardsBlacklist;
use App\LeaderboardRankingTypes;
use App\LeaderboardSnapshots;
use App\Modes;
use App\Players;
use App\PlayerPbs;

class LeaderboardEntries extends Model {
    use IsSchemaTable, HasPartitions, HasTempTable, HasCompositePrimaryKey;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'leaderboard_entries';
    
    /**
     * The primary key associated with the model.
     *
     * @var string
     */
    protected $primaryKey = [
        'leaderboard_snapshot_id',
        'player_pb_id'
    ];
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    
    public static function createTemporaryTable(LeaderboardSources $leaderboard_source): void {
        DB::statement("
            CREATE TEMPORARY TABLE " . static::getTempTableName($leaderboard_source) . " (
                leaderboard_snapshot_id integer,
                player_pb_id integer,
                player_id integer,
                rank integer
            )
            ON COMMIT DROP;
        ");
    }
    
    public static function clear(LeaderboardSources $leaderboard_source, Dates $date): void {    
        DB::delete("
            DELETE FROM " . static::getTableName($leaderboard_source, new DateTime($date->name)) . " le
            USING " . LeaderboardSnapshots::getSchemaTableName($leaderboard_source) . " ls
            WHERE le.leaderboard_snapshot_id = ls.id
                AND ls.date_id = :date_id
        ", [
            ':date_id' => $date->id
        ]);
    }
    
    public static function saveNewTemp(LeaderboardSources $leaderboard_source, DateTime $date): void {
        DB::statement("
            INSERT INTO " . static::getTableName($leaderboard_source, $date) . " (
                leaderboard_snapshot_id,
                player_id,
                player_pb_id,
                rank
            )
            SELECT 
                leaderboard_snapshot_id,
                player_id,
                player_pb_id,
                rank
            FROM " . static::getTempTableName($leaderboard_source) . "
        ");
    } 
    
    public static function updateFromTemp(LeaderboardSources $leaderboard_source): void {}
    
    public static function getPowerRankingsQuery(LeaderboardSources $leaderboard_source, Dates $date): Builder {
        return DB::table(LeaderboardSnapshots::getSchemaTableName($leaderboard_source) . ' AS ls')
            ->select([
                'le.player_pb_id',
                'ppb.player_id',
                'le.rank',
                'ppb.details',
                'lt.name AS leaderboard_type',
                'c.name AS character',
                'l.release_id',
                'l.mode_id',
                'l.seeded_type_id',
                'l.multiplayer_type_id',
                'l.soundtrack_id'
            ])
            ->join(Leaderboards::getSchemaTableName($leaderboard_source) . ' AS l', 'l.id', '=', 'ls.leaderboard_id')
            ->join('leaderboard_types AS lt', 'lt.id', '=', 'l.leaderboard_type_id')
            ->join('characters AS c', 'c.id', '=', 'l.character_id')
            ->join(LeaderboardRankingTypes::getSchemaTableName($leaderboard_source) . ' AS lrt', 'lrt.leaderboard_id', '=', 'l.id')
            ->join('ranking_types AS rt', 'rt.id', '=', 'lrt.ranking_type_id')
            ->join(static::getTableName($leaderboard_source, new DateTime($date->name)) . " AS le", 'le.leaderboard_snapshot_id', '=', 'ls.id')
            ->join(PlayerPbs::getSchemaTableName($leaderboard_source) . " AS ppb", 'ppb.id', '=', 'le.player_pb_id')
            ->where('ls.date_id', $date->id)
            ->where('rt.name', 'power');
    }
    
    public static function getDailyRankingsQuery(LeaderboardSources $leaderboard_source, DateTime $start_date, DateTime $end_date): Builder {
        $query = NULL;
        
        $table_names = static::getTableNames($leaderboard_source, $start_date, $end_date);
        
        if(!empty($table_names)) {
            foreach($table_names as $table_name) {
                $partition_query = DB::table(Leaderboards::getSchemaTableName($leaderboard_source) . ' AS l')
                    ->select([
                        'l.character_id',
                        'l.release_id',
                        'l.mode_id',
                        'l.multiplayer_type_id',
                        'l.soundtrack_id',
                        'd.name AS date',
                        'le.player_id',
                        'le.rank',
                        'ppb.is_win',
                        'ppb.details'
                    ])
                    ->join('dates AS d', 'd.id', '=', 'l.daily_date_id')
                    ->join(LeaderboardRankingTypes::getSchemaTableName($leaderboard_source) . ' AS lrt', 'lrt.leaderboard_id', '=', 'l.id')
                    ->join('ranking_types AS rt', 'rt.id', '=', 'lrt.ranking_type_id')
                    ->join(LeaderboardSnapshots::getSchemaTableName($leaderboard_source) . ' AS ls', function($join) {
                        $join->on('ls.leaderboard_id', '=', 'l.id');
                        $join->on('ls.date_id', '=', 'l.daily_date_id');
                    })
                    ->join("{$table_name} AS le", 'le.leaderboard_snapshot_id', '=', 'ls.id')
                    ->join(PlayerPbs::getSchemaTableName($leaderboard_source) . " AS ppb", 'ppb.id', '=', 'le.player_pb_id')
                    ->whereBetween('d.name', [
                        $start_date->format('Y-m-d'),
                        $end_date->format('Y-m-d')
                    ])
                    ->where('rt.name', 'daily');
                
                if(!isset($query)) {
                    $query = $partition_query;
                }
                else {
                    $query->unionAll($partition_query);
                }
            }
        }
            
        return $query;
    }
    
    public static function getStatsReadQuery(LeaderboardSources $leaderboard_source, Dates $date): Builder {
        return DB::table(LeaderboardSnapshots::getSchemaTableName($leaderboard_source) . ' AS ls')
            ->select([
                'ls.id AS leaderboard_snapshot_id',
                'ppb.details'
            ])
            ->join(static::getTableName($leaderboard_source, new DateTime($date->name)) . " AS le", 'le.leaderboard_snapshot_id', '=', 'ls.id')
            ->join(PlayerPbs::getSchemaTableName($leaderboard_source) . " AS ppb", 'ppb.id', '=', 'le.player_pb_id')
            ->where('ls.date_id', $date->id);
    }
    
    public static function getNonDailyCacheQuery(LeaderboardSources $leaderboard_source, Dates $date): Builder {        
        $entries_table_name = static::getTableName($leaderboard_source, new DateTime($date->name));
    
        $query = DB::table(LeaderboardSnapshots::getSchemaTableName($leaderboard_source) . ' AS ls')
            ->select([
                'l.external_id AS leaderboard_id',
                'le.player_id',
                'le.rank'
            ])
            ->join(Leaderboards::getSchemaTableName($leaderboard_source) . ' AS l', 'l.id', '=', 'ls.leaderboard_id')
            ->join('leaderboard_types AS lt', 'lt.id', '=', 'l.leaderboard_type_id')
            ->leftJoin(LeaderboardsBlacklist::getSchemaTableName($leaderboard_source) . ' AS lb', 'lb.leaderboard_id', '=', 'l.id')
            ->join("{$entries_table_name} AS le", 'le.leaderboard_snapshot_id', '=', 'ls.id')
            ->leftJoin("user_{$leaderboard_source->name}_player AS up", 'up.player_id', '=', 'le.player_id')
            ->leftJoin('users AS u', 'u.id', '=', 'up.user_id')
            ->where('ls.date_id', $date->id)
            ->where('lt.name', '!=', 'daily')
            ->whereNull('lb.leaderboard_id');
            
        ExternalSites::addSiteIdSelectFields($query);
        
        return $query;
    }
    
    public static function getDailyCacheQuery(LeaderboardSources $leaderboard_source, Dates $date): Builder {        
        $entries_table_name = static::getTableName($leaderboard_source, new DateTime($date->name));

        $query = DB::table(Leaderboards::getSchemaTableName($leaderboard_source) . ' AS l')
            ->select([
                'ls.leaderboard_id',
                'l.character_id',
                'l.release_id',
                'l.mode_id',
                'l.multiplayer_type_id',
                'l.soundtrack_id',
                'le.player_id',
                'le.rank'
            ])
            ->join('leaderboard_types AS lt', 'lt.id', '=', 'l.leaderboard_type_id')
            ->leftJoin(LeaderboardsBlacklist::getSchemaTableName($leaderboard_source) . ' AS lb', 'lb.leaderboard_id', '=', 'l.id')
            ->join(LeaderboardSnapshots::getSchemaTableName($leaderboard_source) . ' AS ls', function($join) {
                $join->on('ls.leaderboard_id', '=', 'l.id');
                $join->on('ls.date_id', '=', 'l.daily_date_id');
            })
            ->join("{$entries_table_name} AS le", 'le.leaderboard_snapshot_id', '=', 'ls.id')
            ->leftJoin("user_{$leaderboard_source->name}_player AS up", 'up.player_id', '=', 'le.player_id')
            ->leftJoin('users AS u', 'u.id', '=', 'up.user_id')
            ->where('l.daily_date_id', $date->id)
            ->where('lt.name', '=', 'daily')
            ->whereNull('lb.leaderboard_id');
            
        ExternalSites::addSiteIdSelectFields($query);
        
        return $query;
    }
    
    public static function getNonDailyApiReadQuery(LeaderboardSources $leaderboard_source, string $leaderboard_id, Dates $date): Builder {    
        $entries_table_name = static::getTableName($leaderboard_source, new DateTime($date->name));
    
        $query = DB::table(Leaderboards::getSchemaTableName($leaderboard_source) . ' AS l')
            ->select([
                'le.rank'
            ])
            ->join(LeaderboardSnapshots::getSchemaTableName($leaderboard_source) . ' AS ls', 'ls.leaderboard_id', '=', 'l.id')
            ->join("{$entries_table_name} AS le", 'le.leaderboard_snapshot_id', '=', 'ls.id')
            ->join(Players::getSchemaTableName($leaderboard_source) . ' AS p', 'p.id', '=', 'le.player_id')
            ->join(PlayerPbs::getSchemaTableName($leaderboard_source) . ' AS ppb', 'ppb.id', '=', 'le.player_pb_id');
            
        Players::addSelects($query);
        PlayerPbs::addSelects($query);
            
        PlayerPbs::addJoins($leaderboard_source, $query);        

        Players::addLeftJoins($leaderboard_source, $query);
        PlayerPbs::addLeftJoins($leaderboard_source, $query);
            
        $query->where('l.external_id', $leaderboard_id)
            ->where('ls.date_id', $date->id);        
        
        return $query;
    }
    
    public static function getDailyApiReadQuery(
        LeaderboardSources $leaderboard_source, 
        int $character_id, 
        int $release_id, 
        int $mode_id, 
        int $multiplayer_type_id, 
        int $soundtrack_id,
        Dates $date
    ): Builder {    
        $entries_table_name = static::getTableName($leaderboard_source, new DateTime($date->name));
    
        $query = DB::table(Leaderboards::getSchemaTableName($leaderboard_source) . ' AS l')
            ->select([
                'le.rank',
            ])
            ->join('seeded_types AS st', 'st.id', '=', 'l.seeded_type_id')
            ->join(LeaderboardSnapshots::getSchemaTableName($leaderboard_source) . ' AS ls', 'ls.leaderboard_id', '=', 'l.id')
            ->join("{$entries_table_name} AS le", 'le.leaderboard_snapshot_id', '=', 'ls.id')
            ->join(Players::getSchemaTableName($leaderboard_source) . ' AS p', 'p.id', '=', 'le.player_id')
            ->join(PlayerPbs::getSchemaTableName($leaderboard_source) . ' AS ppb', 'ppb.id', '=', 'le.player_pb_id');
        
        Players::addSelects($query);
        PlayerPbs::addSelects($query);
            
        PlayerPbs::addJoins($leaderboard_source, $query);        

        Players::addLeftJoins($leaderboard_source, $query);
        PlayerPbs::addLeftJoins($leaderboard_source, $query);
        
        $query->where('lt.name', 'daily')
            ->where('l.character_id', $character_id)
            ->where('l.release_id', $release_id)
            ->where('l.mode_id', $mode_id)
            ->where('st.name', '=', 'seeded')
            ->where('l.multiplayer_type_id', $multiplayer_type_id)
            ->where('l.soundtrack_id', $soundtrack_id)
            ->where('l.daily_date_id', $date->id)
            ->where('ls.date_id', $date->id);
        
        return $query;
    }
    
    public static function getPlayerNonDailyApiQuery(
        string $player_id, 
        LeaderboardSources $leaderboard_source,
        int $release_id, 
        int $mode_id, 
        int $seeded_type_id, 
        int $multiplayer_type_id,
        int $soundtrack_id,
        Dates $date
    ): Builder {
        $entries_table_name = static::getTableName($leaderboard_source, new DateTime($date->name));
    
        $query = DB::table(Leaderboards::getSchemaTableName($leaderboard_source) . ' AS l')
            ->select([
                'c.name AS character_name',
                'lt.name AS leaderboard_type_name',
                'le.rank',
            ])
            ->join('characters AS c', 'c.id', '=', 'l.character_id')
            ->join(LeaderboardSnapshots::getSchemaTableName($leaderboard_source) . ' AS ls', 'ls.leaderboard_id', '=', 'l.id')
            ->join("{$entries_table_name} AS le", 'le.leaderboard_snapshot_id', '=', 'ls.id')
            ->join(Players::getSchemaTableName($leaderboard_source) . ' AS p', 'p.id', '=', 'le.player_id')
            ->join(PlayerPbs::getSchemaTableName($leaderboard_source) . ' AS ppb', 'ppb.id', '=', 'le.player_pb_id');
        
        PlayerPbs::addSelects($query);
        PlayerPbs::addJoins($leaderboard_source, $query);
        PlayerPbs::addLeftJoins($leaderboard_source, $query);
        
        $query->where('p.external_id', $player_id)
            ->where('l.release_id', $release_id)
            ->where('l.mode_id', $mode_id)
            ->where('l.seeded_type_id', $seeded_type_id)
            ->where('l.multiplayer_type_id', $multiplayer_type_id)
            ->where('l.soundtrack_id', $soundtrack_id)
            ->where('ls.date_id', $date->id)
            ->orderBy('c.sort_order', 'asc')
            ->orderBy('lt.id', 'asc');
            
        return $query;
    }
    
    public static function getPlayerNonDailyApiReadQuery(
        string $player_id, 
        LeaderboardSources $leaderboard_source,
        int $release_id, 
        int $mode_id, 
        int $seeded_type_id, 
        int $multiplayer_type_id,
        int $soundtrack_id,
        Dates $date
    ): Builder {
        $query = static::getPlayerNonDailyApiQuery(
            $player_id, 
            $leaderboard_source,
            $release_id, 
            $mode_id, 
            $seeded_type_id, 
            $multiplayer_type_id,
            $soundtrack_id,
            $date
        );
        
        $query->where('lt.name', '!=', 'daily');
        
        return $query;
    }
    
    public static function getPlayerCategoryApiReadQuery(
        string $player_id, 
        LeaderboardSources $leaderboard_source,
        int $leaderboard_type_id,
        int $release_id, 
        int $mode_id, 
        int $seeded_type_id, 
        int $multiplayer_type_id,
        int $soundtrack_id,
        Dates $date
    ): Builder {
        $query = static::getPlayerNonDailyApiQuery(
            $player_id, 
            $leaderboard_source,
            $release_id, 
            $mode_id, 
            $seeded_type_id, 
            $multiplayer_type_id,
            $soundtrack_id,
            $date
        );
        
        $query->where('l.leaderboard_type_id', $leaderboard_type_id);
        
        return $query;
    }
    
    public static function getPlayerDailyApiReadQuery(
        string $player_id, 
        LeaderboardSources $leaderboard_source, 
        int $character_id, 
        int $release_id, 
        int $mode_id,
        int $multiplayer_type_id,
        int $soundtrack_id
    ): Builder {            
        $query = DB::table(PlayerPbs::getSchemaTableName($leaderboard_source) . ' AS ppb')
            ->select([
                'd.name AS first_snapshot_date',
                'ppb.first_rank AS rank'
            ])
            ->join(Leaderboards::getSchemaTableName($leaderboard_source) . ' AS l', 'l.id', '=', 'ppb.leaderboard_id')
            ->join('seeded_types AS st', 'st.id', '=', 'l.seeded_type_id')
            ->join('dates AS d', 'd.id', '=', 'l.daily_date_id')
            ->join(Players::getSchemaTableName($leaderboard_source) . ' AS p', 'p.id', '=', 'ppb.player_id');
        
        PlayerPbs::addSelects($query);
        PlayerPbs::addJoins($leaderboard_source, $query);
        PlayerPbs::addLeftJoins($leaderboard_source, $query);
        
        $query->where('p.external_id', $player_id)
            ->where('lt.name', 'daily')
            ->where('l.character_id', $character_id)
            ->where('l.release_id', $release_id)
            ->where('l.mode_id', $mode_id)
            ->where('st.name', 'seeded')
            ->where('l.multiplayer_type_id', $multiplayer_type_id)
            ->where('l.soundtrack_id', $soundtrack_id)
            ->orderBy('d.name', 'desc');
        
        return $query;
    }
}
