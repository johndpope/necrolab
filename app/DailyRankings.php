<?php

namespace App;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Traits\HasTempTable;
use App\Traits\HasManualSequence;
use App\DailyRankingEntries;

class DailyRankings extends Model {
    use HasTempTable, HasManualSequence;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'daily_rankings';
    
    /**
     * The primary key associated with the model.
     *
     * @var string
     */
    protected $primaryKey = 'daily_ranking_id';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    
    public static function getAllIdsByGroupedForDate(DateTime $date) {
        $query = DB::table('daily_rankings')->where('date', $date->format('Y-m-d'));
        
        $rankings_by_id = [];
        
        foreach($query->cursor() as $ranking) {
            $rankings_by_id[$ranking->release_id][$ranking->mode_id][$ranking->daily_ranking_day_type_id] = $ranking->daily_ranking_id;
        }
        
        return $rankings_by_id;
    }
    
    public static function createTemporaryTable() {    
        DB::statement("
            CREATE TEMPORARY TABLE daily_rankings_temp (                
                daily_ranking_id integer,
                created timestamp without time zone,
                date date,
                updated timestamp without time zone,
                daily_ranking_day_type_id smallint,
                release_id smallint,
                mode_id smallint
            )
            ON COMMIT DROP;
        ");
    }
    
    public static function saveTemp() {
        DB::statement("
            INSERT INTO daily_rankings (
                daily_ranking_id,
                created,
                date,
                updated,
                daily_ranking_day_type_id,
                release_id,
                mode_id
            )
            SELECT 
                daily_ranking_id,
                created,
                date,
                updated,
                daily_ranking_day_type_id,
                release_id,
                mode_id
            FROM daily_rankings_temp
            ON CONFLICT (daily_ranking_id) DO 
            UPDATE 
            SET 
                updated = excluded.updated
        ");
    }
    
    public static function updateStats(DateTime $date) {
        $table_name = DailyRankingEntries::getTableName($date);
    
        DB::update("
            WITH daily_ranking_stats AS (
                SELECT 
                    dr.daily_ranking_id,
                    COUNT(dre.steam_user_id) AS players,
                    SUM(dre.first_place_ranks) AS first_place_ranks,
                    SUM(dre.top_5_ranks) AS top_5_ranks,
                    SUM(dre.top_10_ranks) AS top_10_ranks,
                    SUM(dre.top_20_ranks) AS top_20_ranks,
                    SUM(dre.top_50_ranks) AS top_50_ranks,
                    SUM(dre.top_100_ranks) AS top_100_ranks,
                    SUM(dre.total_points) AS total_points,
                    SUM(dre.total_dailies) AS total_dailies,
                    SUM(dre.total_wins) AS total_wins,
                    SUM(dre.sum_of_ranks) AS sum_of_ranks,
                    SUM(dre.total_score) AS total_score
                FROM daily_rankings dr
                JOIN {$table_name} dre ON dre.daily_ranking_id = dr.daily_ranking_id
                WHERE dr.date = :date
                GROUP BY dr.daily_ranking_id
            )
            UPDATE daily_rankings dr
            SET 
                players = drs.players,
                first_place_ranks = drs.first_place_ranks,
                top_5_ranks = drs.top_5_ranks,
                top_10_ranks = drs.top_10_ranks,
                top_20_ranks = drs.top_20_ranks,
                top_50_ranks = drs.top_50_ranks,
                top_100_ranks = drs.top_100_ranks,
                total_points = drs.total_points,
                total_dailies = drs.total_dailies,
                total_wins = drs.total_wins,
                sum_of_ranks = drs.sum_of_ranks,
                total_score = drs.total_score
            FROM daily_ranking_stats drs
            WHERE drs.daily_ranking_id = dr.daily_ranking_id
        ", [
            ':date' => $date->format('Y-m-d')
        ]);
    }
    
    public static function getApiReadQuery(int $release_id, int $daily_ranking_day_type_id) {
        return DB::table('daily_rankings')
            ->select([
                'date'
            ])
            ->where('release_id', $release_id)
            ->where('daily_ranking_day_type_id', $daily_ranking_day_type_id)
            ->orderBy('date', 'desc');
    }
}
