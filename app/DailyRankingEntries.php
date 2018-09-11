<?php

namespace App;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Traits\HasPartitions;
use App\Traits\HasTempTable;
use App\SteamUsers;

class DailyRankingEntries extends Model {
    use HasPartitions, HasTempTable;

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
        'steam_user_id'
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
                daily_ranking_id integer,
                steam_user_id integer,
                first_place_ranks smallint,
                top_5_ranks smallint,
                top_10_ranks smallint,
                top_20_ranks smallint,
                top_50_ranks smallint,
                top_100_ranks smallint,
                total_points double precision,
                total_dailies smallint,
                total_wins smallint,
                sum_of_ranks integer,
                total_score integer,
                rank integer
            )
            ON COMMIT DROP;
        ");
    }
    
    public static function clear(DateTime $date) {    
        DB::delete("
            DELETE FROM " . static::getTableName($date) . " dre
            USING  daily_rankings dr
            WHERE  dre.daily_ranking_id = dr.daily_ranking_id
            AND    dr.date = :date
        ", [
            ':date' => $date->format('Y-m-d')
        ]);
    }
    
    public static function saveTemp(DateTime $date) {
        DB::statement("
            INSERT INTO " . static::getTableName($date) . " (
                daily_ranking_id,
                steam_user_id,
                first_place_ranks,
                top_5_ranks,
                top_10_ranks,
                top_20_ranks,
                top_50_ranks,
                top_100_ranks,
                total_points,
                total_dailies,
                total_wins,
                sum_of_ranks,
                total_score,
                rank
            )
            SELECT 
                daily_ranking_id,
                steam_user_id,
                first_place_ranks,
                top_5_ranks,
                top_10_ranks,
                top_20_ranks,
                top_50_ranks,
                top_100_ranks,
                total_points,
                total_dailies,
                total_wins,
                sum_of_ranks,
                total_score,
                rank
            FROM " . static::getTempTableName() . "
        ");
    }
    
    public static function getCacheQuery(DateTime $date) {
        $entries_table_name = static::getTableName($date);

        $query = DB::table('daily_rankings AS dr')
            ->select([
                'dr.release_id',
                'dr.mode_id',
                'dr.daily_ranking_day_type_id',
                'dre.steam_user_id',
                'dre.rank'
            ])
            ->join("{$entries_table_name} AS dre", 'dre.daily_ranking_id', '=', 'dr.daily_ranking_id')
            ->join('steam_users AS su', 'su.steam_user_id', '=', 'dre.steam_user_id')
            ->where('dr.date', $date->format('Y-m-d'));
            
        ExternalSites::addSiteIdSelectFields($query);
        
        return $query;
    }
    
    public static function getApiReadQuery(int $release_id, int $mode_id, int $daily_ranking_day_type_id, DateTime $date) {
        $entries_table_name = static::getTableName($date);

        $query = DB::table('daily_rankings AS dr')
            ->select([
                'dre.rank',
                'dre.first_place_ranks',
                'dre.top_5_ranks',
                'dre.top_10_ranks',
                'dre.top_20_ranks',
                'dre.top_50_ranks',
                'dre.top_100_ranks',
                'dre.total_points',
                'dre.total_score',
                'dre.total_dailies',
                'dre.total_wins',
                'dre.sum_of_ranks',
                'dre.steam_user_id'
            ])
            ->join("{$entries_table_name} AS dre", 'dre.daily_ranking_id', '=', 'dr.daily_ranking_id')
            ->join('steam_users AS su', 'su.steam_user_id', '=', 'dre.steam_user_id')
            ->where('dr.date', $date->format('Y-m-d'))
            ->where('dr.release_id', $release_id)
            ->where('dr.mode_id', $mode_id)
            ->where('dr.daily_ranking_day_type_id', $daily_ranking_day_type_id);
            
        SteamUsers::addSelects($query);
        SteamUsers::addLeftJoins($query);
        
        return $query;
    }
    
    public static function getSteamUserApiReadQuery(string $steamid, int $release_id, int $mode_id, int $daily_ranking_day_type_id) {
        $release = Releases::getById($release_id);
        
        $start_date = new DateTime($release['start_date']);
        $end_date = new DateTime($release['end_date']);
    
        $query = NULL;
        
        $table_names = static::getTableNames($start_date, $end_date);
        
        if(!empty($table_names)) {
            foreach($table_names as $table_name) {                    
                $partition_query = DB::table('daily_rankings AS dr')
                    ->select([
                        'dr.date',
                        'dre.rank',
                        'dre.first_place_ranks',
                        'dre.top_5_ranks',
                        'dre.top_10_ranks',
                        'dre.top_20_ranks',
                        'dre.top_50_ranks',
                        'dre.top_100_ranks',
                        'dre.total_points',
                        'dre.total_score',
                        'dre.total_dailies',
                        'dre.total_wins',
                        'dre.sum_of_ranks'
                    ])
                    ->join("{$table_name} AS dre", 'dre.daily_ranking_id', '=', 'dr.daily_ranking_id')
                    ->join('steam_users AS su', 'su.steam_user_id', '=', 'dre.steam_user_id')
                    ->where('su.steamid', $steamid)
                    ->whereBetween('dr.date', [
                        $start_date->format('Y-m-d'),
                        $end_date->format('Y-m-d')
                    ])
                    ->where('dr.release_id', $release_id)
                    ->where('dr.mode_id', $mode_id)
                    ->where('dr.daily_ranking_day_type_id', $daily_ranking_day_type_id);
                
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
