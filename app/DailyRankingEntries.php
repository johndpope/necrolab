<?php

namespace App;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Traits\HasPartitions;
use App\Traits\HasTempTable;

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
}