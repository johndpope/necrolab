<?php

namespace App;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\ExternalSites;
use App\Traits\HasPartitions;
use App\Traits\HasTempTable;
use App\Modes;
use App\SteamUsers;
use App\SteamUserPbs;

class LeaderboardEntries extends Model {
    use HasPartitions, HasTempTable;

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
        'steam_user_pb_id',
        'rank'
    ];
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    
    public static function createTemporaryTable() {
        DB::statement("
            CREATE TEMPORARY TABLE " . static::getTempTableName() . " (
                leaderboard_snapshot_id integer NOT NULL,
                steam_user_id integer NOT NULL,
                steam_user_pb_id integer NOT NULL,
                rank integer NOT NULL
            )
            ON COMMIT DROP;
        ");
    }
    
    public static function clear(DateTime $date) {    
        DB::delete("
            DELETE FROM " . static::getTableName($date) . " le
            USING  leaderboard_snapshots ls
            WHERE  le.leaderboard_snapshot_id = ls.leaderboard_snapshot_id
            AND    ls.date = :date
        ", [
            ':date' => $date->format('Y-m-d')
        ]);
    }
    
    public static function saveTemp(DateTime $date) {
        DB::statement("
            INSERT INTO " . static::getTableName($date) . " (
                leaderboard_snapshot_id,
                steam_user_id,
                steam_user_pb_id,
                rank
            )
            SELECT 
                leaderboard_snapshot_id,
                steam_user_id,
                steam_user_pb_id,
                rank
            FROM " . static::getTempTableName() . "
        ");
    } 
    
    public static function getPowerRankingsQuery(DateTime $date) {
        return DB::table('leaderboard_snapshots AS ls')
            ->select([
                'lt.name AS leaderboard_type',
                'l.release_id',
                'l.mode_id',
                'l.seeded_type_id',
                'c.name AS character_name',
                'le.steam_user_pb_id',
                'sup.score',
                'sup.time',
                'sup.win_count',
                'sup.steam_user_id',
                'le.rank',
            ])
            ->join('leaderboards AS l', 'l.leaderboard_id', '=', 'ls.leaderboard_id')
            ->join('leaderboard_types AS lt', 'lt.leaderboard_type_id', '=', 'l.leaderboard_type_id')
            ->join('characters AS c', 'c.character_id', '=', 'l.character_id')
            ->join('leaderboard_ranking_types AS lrt', 'lrt.leaderboard_id', '=', 'l.leaderboard_id')
            ->join('ranking_types AS rt', 'rt.id', '=', 'lrt.ranking_type_id')
            ->join(static::getTableName($date) . " AS le", 'le.leaderboard_snapshot_id', '=', 'ls.leaderboard_snapshot_id')
            ->join("steam_user_pbs AS sup", 'sup.steam_user_pb_id', '=', 'le.steam_user_pb_id')
            ->where('ls.date', $date->format('Y-m-d'))
            ->where('rt.name', 'power');
    }
    
    public static function getDailyRankingsQuery(DateTime $start_date, DateTime $end_date) {
        $query = NULL;
        
        $table_names = static::getTableNames($start_date, $end_date);
        
        if(!empty($table_names)) {
            foreach($table_names as $table_name) {
                $partition_query = DB::table('leaderboard_snapshots AS ls')
                    ->select([
                        'l.release_id',
                        'l.mode_id',
                        'l.daily_date',
                        'sup.steam_user_id',
                        'le.rank',
                        'sup.is_win',
                        'sup.score'
                    ])
                    ->join('leaderboards AS l', 'l.leaderboard_id', '=', 'ls.leaderboard_id')
                    ->join('leaderboard_ranking_types AS lrt', 'lrt.leaderboard_id', '=', 'l.leaderboard_id')
                    ->join('ranking_types AS rt', 'rt.id', '=', 'lrt.ranking_type_id')
                    ->join("{$table_name} AS le", 'le.leaderboard_snapshot_id', '=', 'ls.leaderboard_snapshot_id')
                    ->join('steam_user_pbs AS sup', 'sup.steam_user_pb_id', '=', 'le.steam_user_pb_id')
                    ->whereBetween('ls.date', [
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
    
    public static function getNonDailyCacheQuery(DateTime $date) {        
        $entries_table_name = static::getTableName($date);
    
        $query = DB::table('leaderboard_snapshots AS ls')
            ->select([
                'l.lbid',
                'le.steam_user_id',
                'le.rank'
            ])
            ->join('leaderboards AS l', 'l.leaderboard_id', '=', 'ls.leaderboard_id')
            ->join('leaderboard_types AS lt', 'lt.leaderboard_type_id', '=', 'l.leaderboard_type_id')
            ->leftJoin('leaderboards_blacklist AS lb', 'lb.leaderboard_id', '=', 'l.leaderboard_id')
            ->join("{$entries_table_name} AS le", 'le.leaderboard_snapshot_id', '=', 'ls.leaderboard_snapshot_id')
            ->join('steam_users AS su', 'su.steam_user_id', '=', 'le.steam_user_id')
            ->leftJoin('users AS u', 'u.steam_user_id', '=', 'su.steam_user_id')
            ->where('ls.date', $date->format('Y-m-d'))
            ->where('lt.name', '!=', 'daily')
            ->whereNull('lb.leaderboards_blacklist_id');
            
        ExternalSites::addSiteIdSelectFields($query);
        
        return $query;
    }
    
    public static function getDailyCacheQuery(DateTime $date) {        
        $entries_table_name = static::getTableName($date);

        $query = DB::table('leaderboard_snapshots AS ls')
            ->select([
                'ls.leaderboard_id',
                'l.release_id',
                'l.daily_date',
                'l.mode_id',
                'le.steam_user_id',
                'le.rank'
            ])
            ->join('leaderboards AS l', 'l.leaderboard_id', '=', 'ls.leaderboard_id')
            ->join('leaderboard_types AS lt', 'lt.leaderboard_type_id', '=', 'l.leaderboard_type_id')
            ->join('multiplayer_types AS mt', 'mt.id', '=', 'l.multiplayer_type_id')
            ->leftJoin('leaderboards_blacklist AS lb', 'lb.leaderboard_id', '=', 'l.leaderboard_id')
            ->join("{$entries_table_name} AS le", 'le.leaderboard_snapshot_id', '=', 'ls.leaderboard_snapshot_id')
            ->join('steam_users AS su', 'su.steam_user_id', '=', 'le.steam_user_id')
            ->leftJoin('users AS u', 'u.steam_user_id', '=', 'su.steam_user_id')
            ->where('ls.date', $date->format('Y-m-d'))
            ->where('lt.name', '=', 'daily')
            ->where('mt.name', '=', 'single')
            ->whereNull('lb.leaderboards_blacklist_id');
            
        ExternalSites::addSiteIdSelectFields($query);
        
        return $query;
    }
    
    public static function getNonDailyApiReadQuery(int $lbid, DateTime $date) {    
        $entries_table_name = static::getTableName($date);
    
        $query = DB::table('leaderboards AS l')
            ->select([
                'lt.name AS leaderboard_type',
                'le.rank'
            ])
            ->join('leaderboard_types AS lt', 'lt.leaderboard_type_id', '=', 'l.leaderboard_type_id')
            ->join('leaderboard_snapshots AS ls', 'ls.leaderboard_id', '=', 'l.leaderboard_id')
            ->join("{$entries_table_name} AS le", 'le.leaderboard_snapshot_id', '=', 'ls.leaderboard_snapshot_id')
            ->join('steam_users AS su', 'su.steam_user_id', '=', 'le.steam_user_id')
            ->join('steam_user_pbs AS sup', 'sup.steam_user_pb_id', '=', 'le.steam_user_pb_id');
            
        SteamUsers::addSelects($query);
        SteamUserPbs::addSelects($query);
            
        SteamUserPbs::addJoins($query);        

        SteamUsers::addLeftJoins($query);
        SteamUserPbs::addLeftJoins($query);
            
        $query->where('l.lbid', $lbid)
            ->where('ls.date', $date->format('Y-m-d'));        
        
        return $query;
    }
    
    public static function getDailyApiReadQuery(int $release_id, DateTime $date) {    
        $entries_table_name = static::getTableName($date);
    
        $query = DB::table('leaderboards AS l')
            ->select([
                'lt.name AS leaderboard_type',
                'le.rank',
            ])
            ->join('leaderboard_types AS lt', 'lt.leaderboard_type_id', '=', 'l.leaderboard_type_id')
            ->join('leaderboard_snapshots AS ls', function($join) {
                $join->on('ls.leaderboard_id', '=', 'l.leaderboard_id')
                    ->on('ls.date', '=', 'l.daily_date');
            })
            ->join("{$entries_table_name} AS le", 'le.leaderboard_snapshot_id', '=', 'ls.leaderboard_snapshot_id')
            ->join('steam_users AS su', 'su.steam_user_id', '=', 'le.steam_user_id')
            ->join('steam_user_pbs AS sup', 'sup.steam_user_pb_id', '=', 'le.steam_user_pb_id');
        
        SteamUsers::addSelects($query);
        SteamUserPbs::addSelects($query);
            
        SteamUserPbs::addJoins($query);        

        SteamUsers::addLeftJoins($query);
        SteamUserPbs::addLeftJoins($query);
        
        $query->where('l.release_id', $release_id)
            ->where('lt.name', 'daily')
            ->where('l.daily_date', $date->format('Y-m-d'));
        
        return $query;
    }
    
    public static function getSteamUserNonDailyApiQuery(
        string $steamid, 
        DateTime $date, 
        int $release_id, 
        int $mode_id, 
        int $seeded_type_id, 
        int $multiplayer_type_id,
        int $soundtrack_id
    ) {
        $entries_table_name = static::getTableName($date);
    
        $query = DB::table('leaderboards AS l')
            ->select([
                'c.name AS character_name',
                'lt.name AS leaderboard_type',
                'le.rank',
            ])
            ->join('characters AS c', 'c.character_id', '=', 'l.character_id')
            ->join('leaderboard_types AS lt', 'lt.leaderboard_type_id', '=', 'l.leaderboard_type_id')
            ->join('leaderboard_snapshots AS ls', 'ls.leaderboard_id', '=', 'l.leaderboard_id')
            ->join("{$entries_table_name} AS le", 'le.leaderboard_snapshot_id', '=', 'ls.leaderboard_snapshot_id')
            ->join('steam_users AS su', 'su.steam_user_id', '=', 'le.steam_user_id')
            ->join('steam_user_pbs AS sup', 'sup.steam_user_pb_id', '=', 'le.steam_user_pb_id');
        
        SteamUserPbs::addSelects($query);
        SteamUserPbs::addJoins($query);
        SteamUserPbs::addLeftJoins($query);
        
        $query->where('l.release_id', $release_id)
            ->where('l.mode_id', $mode_id)
            ->where('l.seeded_type_id', $seeded_type_id)
            ->where('l.multiplayer_type_id', $multiplayer_type_id)
            ->where('l.soundtrack_id', $soundtrack_id)
            ->where('ls.date', $date->format('Y-m-d'))
            ->where('su.steamid', $steamid)
            ->orderBy('c.sort_order', 'asc')
            ->orderBy('lt.leaderboard_type_id', 'asc');
            
        return $query;
    }
    
    public static function getSteamUserNonDailyApiReadQuery(
        string $steamid, 
        DateTime $date, 
        int $release_id, 
        int $mode_id, 
        int $seeded_type_id, 
        int $multiplayer_type_id,
        int $soundtrack_id
    ) {
        $query = static::getSteamUserNonDailyApiQuery(
            $steamid, 
            $date, 
            $release_id, 
            $mode_id, 
            $seeded_type_id, 
            $multiplayer_type_id,
            $soundtrack_id
        );
        
        $query->where('lt.name', '!=', 'daily');
        
        return $query;
    }
    
    public static function getSteamUserScoreApiReadQuery(
        string $steamid, 
        DateTime $date, 
        int $release_id, 
        int $mode_id, 
        int $seeded_type_id, 
        int $multiplayer_type_id,
        int $soundtrack_id
    ) {
        $query = static::getSteamUserNonDailyApiQuery(
            $steamid, 
            $date, 
            $release_id, 
            $mode_id, 
            $seeded_type_id, 
            $multiplayer_type_id,
            $soundtrack_id
        );
        
        $query->where('lt.name', 'score');
        
        return $query;
    }
    
    public static function getSteamUserSpeedApiReadQuery(
        string $steamid, 
        DateTime $date, 
        int $release_id, 
        int $mode_id, 
        int $seeded_type_id, 
        int $multiplayer_type_id,
        int $soundtrack_id
    ) {
        $query = static::getSteamUserNonDailyApiQuery(
            $steamid, 
            $date, 
            $release_id, 
            $mode_id, 
            $seeded_type_id, 
            $multiplayer_type_id,
            $soundtrack_id
        );
        
        $query->where('lt.name', 'speed');
        
        return $query;
    }
    
    public static function getSteamUserDeathlessApiReadQuery(
        string $steamid, 
        DateTime $date, 
        int $release_id, 
        int $mode_id, 
        int $seeded_type_id, 
        int $multiplayer_type_id,
        int $soundtrack_id
    ) {
        $mode_id = Modes::getByName('normal')->mode_id;
    
        $query = static::getSteamUserNonDailyApiQuery(
            $steamid, 
            $date, 
            $release_id, 
            $mode_id, 
            $seeded_type_id, 
            $multiplayer_type_id,
            $soundtrack_id
        );
        
        $query->where('lt.name', 'deathless');
        
        return $query;
    }
    
    public static function getSteamUserDailyApiReadQuery(string $steamid, int $release_id) {            
        $query = DB::table('steam_user_pbs AS sup')
            ->select([
                'l.daily_date AS first_snapshot_date',
                'lt.name AS leaderboard_type',
                'sup.first_rank AS rank'
            ])
            ->join('leaderboards AS l', 'l.leaderboard_id', '=', 'sup.leaderboard_id')
            ->join('leaderboard_types AS lt', 'lt.leaderboard_type_id', '=', 'l.leaderboard_type_id')
            ->join('steam_users AS su', 'su.steam_user_id', '=', 'sup.steam_user_id');
        
        SteamUserPbs::addSelects($query);
        SteamUserPbs::addJoins($query);
        SteamUserPbs::addLeftJoins($query);
        
        $query->where('su.steamid', $steamid)
            ->where('l.release_id', $release_id)
            ->where('lt.name', 'daily')
            ->orderBy('l.daily_date', 'desc');
        
        return $query;
    }
}
