<?php

namespace App;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Traits\HasTempTable;
use App\Traits\HasManualSequence;

class PowerRankings extends Model {
    use HasTempTable, HasManualSequence;

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
            CREATE TEMPORARY TABLE " . static::getTempTableName() . " (
                power_ranking_id integer,
                created timestamp without time zone,
                date date,
                updated timestamp without time zone,
                release_id smallint,
                latest_steam_replay_version_id smallint,
                mode_id smallint,
                seeded_type_id smallint,
                players integer,
                categories bytea,
                characters bytea
            )
            ON COMMIT DROP;
        ");
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
                seeded_type_id
            )
            SELECT 
                power_ranking_id,
                created,
                date,
                updated,
                release_id,
                latest_steam_replay_version_id,
                mode_id,
                seeded_type_id
            FROM " . static::getTempTableName() . "
            ON CONFLICT (power_ranking_id) DO 
            UPDATE 
            SET 
                updated = excluded.updated
        ");
    }
    
    public static function updateFromTemp() {    
        DB::update("
            UPDATE power_rankings pr
            SET 
                players = prt.players,
                categories = prt.categories,
                characters = prt.characters
            FROM " . static::getTempTableName() . " prt
            WHERE pr.power_ranking_id = prt.power_ranking_id
        ");
    }
    
    public static function getAllIdsByGroupedForDate(DateTime $date) {
        $query = DB::table('power_rankings')->where('date', $date->format('Y-m-d'));
        
        $rankings_by_id = [];
        
        foreach($query->cursor() as $ranking) {
            $rankings_by_id[$ranking->release_id][$ranking->mode_id][$ranking->seeded_type_id] = $ranking->power_ranking_id;
        }
        
        return $rankings_by_id;
    }
    
    public static function getApiReadQuery(int $release_id, int $mode_id, int $seeded_type_id) {
        return DB::table('power_rankings')
            ->select([
                'date',
                'players',
                'categories',
                'characters'
            ])
            ->where('release_id', $release_id)
            ->where('mode_id', $mode_id)
            ->where('seeded_type_id', $seeded_type_id)
            ->orderBy('date', 'desc');
    }
}
