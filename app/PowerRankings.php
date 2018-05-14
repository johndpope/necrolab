<?php

namespace App;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Traits\HasTempTable;

class PowerRankings extends Model {
    use HasTempTable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'power_rankings';
    
    /**
     * The primary key associated with the model.
     *
     * @var string
     */
    protected $primaryKey = 'power_ranking_id';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    
    public static function createTemporaryTable() {    
        DB::statement("
            CREATE TEMPORARY TABLE power_rankings_temp (
                power_ranking_id integer,
                created timestamp without time zone,
                date date,
                updated timestamp without time zone,
                release_id smallint,
                latest_steam_replay_version_id smallint,
                mode_id smallint,
                seeded smallint
            )
            ON COMMIT DROP;
        ");
    }
    
    public static function getNewRecordId() {
        $new_record_id = DB::selectOne("
            SELECT nextval('power_rankings_seq'::regclass) AS id
        ");
        
        return $new_record_id->id;
    }
    
    public static function saveTemp() {
        DB::statement("
            INSERT INTO power_rankings (
                power_ranking_id,
                created,
                date,
                updated,
                release_id,
                latest_steam_replay_version_id,
                mode_id,
                seeded
            )
            SELECT 
                power_ranking_id,
                created,
                date,
                updated,
                release_id,
                latest_steam_replay_version_id,
                mode_id,
                seeded
            FROM power_rankings_temp
            ON CONFLICT (power_ranking_id) DO 
            UPDATE 
            SET 
                updated = excluded.updated
        ");
    }
    
    public static function getAllIdsByGroupedForDate(DateTime $date) {
        $query = DB::table('power_rankings')->where('date', $date->format('Y-m-d'));
        
        $rankings_by_id = [];
        
        foreach($query->cursor() as $ranking) {
            $rankings_by_id[$ranking->release_id][$ranking->mode_id][$ranking->seeded] = $ranking->power_ranking_id;
        }
        
        return $rankings_by_id;
    }
}