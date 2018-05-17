<?php

namespace App;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Traits\HasTempTable;
use App\Traits\HasManualSequence;

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
}
