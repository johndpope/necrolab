<?php

namespace App;

use DateTime;
use PDO;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use App\Traits\IsSchemaTable;
use App\Traits\HasPartitions;
use App\Traits\HasTempTable;
use App\Players;
use App\LeaderboardSources;
use App\Dates;
use App\DailyRankings;
use App\Characters;
use App\Releases;
use App\Modes;
use App\MultiplayerTypes;
use App\Soundtracks;
use App\DailyRankingDayTypes;

class DailyRankingEntries extends Model {
    use IsSchemaTable, HasPartitions, HasTempTable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'daily_ranking_entries';
    
    /**
     * This table has a composite primary key.
     *
     * @var string
     */
    protected $primaryKey = [
        'daily_ranking_id',
        'player_id'
    ];
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    
    public static function getTempInsertQueueBindFlags(): array {
        return [
            PDO::PARAM_INT,
            PDO::PARAM_INT,
            PDO::PARAM_INT,
            PDO::PARAM_INT,
            PDO::PARAM_INT,
            PDO::PARAM_INT,
            PDO::PARAM_INT,
            PDO::PARAM_INT,
            PDO::PARAM_INT,
            PDO::PARAM_INT,
            PDO::PARAM_INT,
            PDO::PARAM_INT,
            PDO::PARAM_LOB
        ];
    }
    
    public static function createTemporaryTable(LeaderboardSources $leaderboard_source): void {
        DB::statement("
            CREATE TEMPORARY TABLE " . static::getTempTableName($leaderboard_source) . " (
                sum_of_ranks bigint,
                daily_ranking_id integer,
                player_id integer,
                rank integer,
                first_place_ranks smallint,
                top_5_ranks smallint,
                top_10_ranks smallint,
                top_20_ranks smallint,
                top_50_ranks smallint,
                top_100_ranks smallint,
                dailies smallint,
                wins smallint,
                details bytea
            )
            ON COMMIT DROP;
        ");
    }
    
    public static function clear(LeaderboardSources $leaderboard_source, Dates $date): void {    
        DB::delete("
            DELETE FROM " . static::getTableName($leaderboard_source, new DateTime($date->name)) . " dre
            USING  " . DailyRankings::getSchemaTableName($leaderboard_source) . " dr
            WHERE  dre.daily_ranking_id = dr.id
            AND    dr.date_id = :date_id
        ", [
            ':date_id' => $date->id
        ]);
    }
    
    public static function saveNewTemp(LeaderboardSources $leaderboard_source, Dates $date): void {
        DB::statement("
            INSERT INTO " . static::getTableName($leaderboard_source, new DateTime($date->name)) . " (
                sum_of_ranks,
                daily_ranking_id,
                player_id,
                rank,
                first_place_ranks,
                top_5_ranks,
                top_10_ranks,
                top_20_ranks,
                top_50_ranks,
                top_100_ranks,
                dailies,
                wins,
                details
            )
            SELECT 
                sum_of_ranks,
                daily_ranking_id,
                player_id,
                rank,
                first_place_ranks,
                top_5_ranks,
                top_10_ranks,
                top_20_ranks,
                top_50_ranks,
                top_100_ranks,
                dailies,
                wins,
                details
            FROM " . static::getTempTableName($leaderboard_source) . "
        ");
    }
    
    public static function updateFromTemp(): void {}
    
    public static function getCacheQuery(LeaderboardSources $leaderboard_source, Dates $date): Builder {
        $entries_table_name = static::getTableName($leaderboard_source, new DateTime($date->name));

        $query = DB::table(DailyRankings::getSchemaTableName($leaderboard_source) . ' AS dr')
            ->select([
                'dr.character_id',
                'dr.release_id',
                'dr.mode_id',
                'dr.multiplayer_type_id',
                'dr.soundtrack_id',
                'dr.daily_ranking_day_type_id',
                'dre.player_id',
                'dre.rank'
            ])
            ->join("{$entries_table_name} AS dre", 'dre.daily_ranking_id', '=', 'dr.id')
            ->leftJoin("user_{$leaderboard_source->name}_player AS up", 'up.player_id', '=', 'dre.player_id')
            ->leftJoin('users AS u', 'u.id', '=', 'up.user_id')
            ->where('dr.date_id', $date->id);
            
        ExternalSites::addSiteIdSelectFields($query);
        
        return $query;
    }
    
    public static function getStatsReadQuery(LeaderboardSources $leaderboard_source, Dates $date): Builder {
        $entries_table_name = static::getTableName($leaderboard_source, new DateTime($date->name));

        $query = DB::table(DailyRankings::getSchemaTableName($leaderboard_source) . " AS dr")
            ->select([
                'dr.id AS daily_ranking_id',
                'dre.rank',
                'dre.dailies',
                'dre.wins',
                'dre.details'
            ])
            ->join("{$entries_table_name} AS dre", 'dre.daily_ranking_id', '=', 'dr.id')
            ->where('dr.date_id', $date->id);
        
        return $query;
    }
    
    public static function getApiReadQuery(
        LeaderboardSources $leaderboard_source,
        Characters $character,
        Releases $release,
        Modes $mode,
        MultiplayerTypes $multiplayer_type,
        Soundtracks $soundtrack,
        DailyRankingDayTypes $daily_ranking_day_type,
        Dates $date
    ): Builder {
        $entries_table_name = static::getTableName($leaderboard_source, new DateTime($date->name));

        $query = DB::table(DailyRankings::getSchemaTableName($leaderboard_source) . ' AS dr')
            ->select([
                'dre.rank',
                'dre.first_place_ranks',
                'dre.top_5_ranks',
                'dre.top_10_ranks',
                'dre.top_20_ranks',
                'dre.top_50_ranks',
                'dre.top_100_ranks',
                'dre.dailies',
                'dre.wins',
                'dre.sum_of_ranks',
                'dre.details',
                'dre.player_id'
            ])
            ->join("{$entries_table_name} AS dre", 'dre.daily_ranking_id', '=', 'dr.id')
            ->join(Players::getSchemaTableName($leaderboard_source) . ' AS p', 'p.id', '=', 'dre.player_id')
            ->where('dr.character_id', $character->id)
            ->where('dr.release_id', $release->id)
            ->where('dr.mode_id', $mode->id)
            ->where('dr.multiplayer_type_id', $multiplayer_type->id)
            ->where('dr.soundtrack_id', $soundtrack->id)
            ->where('dr.daily_ranking_day_type_id', $daily_ranking_day_type->id)
            ->where('dr.date_id', $date->id);
            
        Players::addSelects($query);
        Players::addLeftJoins($leaderboard_source, $query);
        
        return $query;
    }
    
    public static function getPlayerApiReadQuery(
        LeaderboardSources $leaderboard_source,
        string $player_id, 
        Characters $character,
        Releases $release,
        Modes $mode,
        MultiplayerTypes $multiplayer_type,
        Soundtracks $soundtrack,
        DailyRankingDayTypes $daily_ranking_day_type
    ): Builder {        
        $start_date = new DateTime($release['start_date']);
        $end_date = new DateTime($release['end_date']);
    
        $query = NULL;
        
        $table_names = static::getTableNames($leaderboard_source, $start_date, $end_date);
        
        if(!empty($table_names)) {
            foreach($table_names as $table_name) {                    
                $partition_query = DB::table(DailyRankings::getSchemaTableName($leaderboard_source) . ' AS dr')
                    ->select([
                        'd.name AS date',
                        'dre.rank',
                        'dre.first_place_ranks',
                        'dre.top_5_ranks',
                        'dre.top_10_ranks',
                        'dre.top_20_ranks',
                        'dre.top_50_ranks',
                        'dre.top_100_ranks',
                        'dre.dailies',
                        'dre.wins',
                        'dre.sum_of_ranks',
                        'dre.details'
                    ])
                    ->join("dates AS d", 'd.id', '=', 'dr.date_id')
                    ->join("{$table_name} AS dre", 'dre.daily_ranking_id', '=', 'dr.id')
                    ->join(Players::getSchemaTableName($leaderboard_source) . ' AS p', 'p.id', '=', 'dre.player_id')
                    ->where('dr.character_id', $character->id)
                    ->where('dr.release_id', $release->id)
                    ->where('dr.mode_id', $mode->id)
                    ->where('dr.multiplayer_type_id', $multiplayer_type->id)
                    ->where('dr.soundtrack_id', $soundtrack->id)
                    ->where('dr.daily_ranking_day_type_id', $daily_ranking_day_type->id)
                    ->whereBetween('d.name', [
                        $start_date->format('Y-m-d'),
                        $end_date->format('Y-m-d')
                    ])
                    ->where('p.external_id', $player_id);
                
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
}
