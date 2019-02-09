<?php

namespace App;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Traits\IsSchemaTable;
use App\Traits\HasTempTable;
use App\Traits\HasManualSequence;
use App\LeaderboardSources;
use App\Dates;

class PowerRankings extends Model {
    use IsSchemaTable, HasTempTable, HasManualSequence;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'power_rankings';
    
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    
    public static function createTemporaryTable(LeaderboardSources $leaderboard_source): void {    
        DB::statement("
            CREATE TEMPORARY TABLE " . static::getTempTableName($leaderboard_source) . " (
                created timestamp without time zone,
                updated timestamp without time zone,
                id integer,
                players integer,
                release_id smallint,
                mode_id smallint,
                seeded_type_id smallint,
                multiplayer_type_id smallint,
                soundtrack_id smallint,
                date_id smallint,
                categories jsonb,
                characters jsonb
            )
            ON COMMIT DROP;
        ");
    }
    
    public static function saveNewTemp(LeaderboardSources $leaderboard_source): void {
        DB::statement("
            INSERT INTO " . static::getSchemaTableName($leaderboard_source) . " (
                created,
                updated,
                id,
                players,
                release_id,
                mode_id,
                seeded_type_id,
                multiplayer_type_id,
                soundtrack_id,
                date_id
            )
            SELECT 
                created,
                updated,
                id,
                players,
                release_id,
                mode_id,
                seeded_type_id,
                multiplayer_type_id,
                soundtrack_id,
                date_id
            FROM " . static::getTempTableName($leaderboard_source) . "
            ON CONFLICT (id) DO 
            UPDATE 
            SET 
                updated = excluded.updated
        ");
    }
    
    public static function updateFromTemp(LeaderboardSources $leaderboard_source): void {    
        DB::update("
            UPDATE " . static::getSchemaTableName($leaderboard_source) . " pr
            SET 
                players = prt.players,
                categories = prt.categories,
                characters = prt.characters
            FROM " . static::getTempTableName($leaderboard_source) . " prt
            WHERE pr.id = prt.id
        ");
    }
    
    public static function getAllIdsByGroupedForDate(LeaderboardSources $leaderboard_source, Dates $date): array {
        $query = DB::table(static::getSchemaTableName($leaderboard_source))->where('date_id', $date->id);
        
        $rankings_by_id = [];
        
        foreach($query->cursor() as $ranking) {
            $rankings_by_id[$ranking->release_id][$ranking->mode_id][$ranking->seeded_type_id][$ranking->multiplayer_type_id][$ranking->soundtrack_id] = $ranking->id;
        }
        
        return $rankings_by_id;
    }
    
    public static function getApiReadQuery(
        LeaderboardSources $leaderboard_source,
        int $release_id, 
        int $mode_id, 
        int $seeded_type_id, 
        int $multiplayer_type_id, 
        int $soundtrack_id
    ): Builder {
        return DB::table(static::getSchemaTableName($leaderboard_source) . ' AS pr')
            ->select([
                'd.name AS date',
                'pr.players',
                'pr.categories',
                'pr.characters'
            ])
            ->join('dates AS d', 'd.id', '=', 'pr.date_id')
            ->where('pr.release_id', $release_id)
            ->where('pr.mode_id', $mode_id)
            ->where('pr.seeded_type_id', $seeded_type_id)
            ->where('pr.multiplayer_type_id', $multiplayer_type_id)
            ->where('pr.soundtrack_id', $soundtrack_id)
            ->orderBy('d.name', 'desc');
    }
}
