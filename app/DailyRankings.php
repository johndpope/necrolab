<?php

namespace App;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use App\Traits\IsSchemaTable;
use App\Traits\HasTempTable;
use App\Traits\HasManualSequence;
use App\DailyRankingEntries;
use App\LeaderboardSources;
use App\Dates;
use App\Characters;
use App\Releases;
use App\Modes;
use App\MultiplayerTypes;
use App\Soundtracks;
use App\DailyRankingDayTypes;

class DailyRankings extends Model {
    use IsSchemaTable, HasTempTable, HasManualSequence;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'daily_rankings';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    
    public static function getAllIdsByGroupedForDate(LeaderboardSources $leaderboard_source, Dates $date): array {
        $query = DB::table(static::getSchemaTableName($leaderboard_source))->where('date_id', $date->id);
        
        $rankings_by_id = [];
        
        foreach($query->cursor() as $ranking) {
            $rankings_by_id[$ranking->character_id][$ranking->release_id][$ranking->mode_id][$ranking->multiplayer_type_id][$ranking->soundtrack_id][$ranking->daily_ranking_day_type_id] = $ranking->id;
        }
        
        return $rankings_by_id;
    }
    
    public static function createTemporaryTable(LeaderboardSources $leaderboard_source): void {    
        DB::statement("
            CREATE TEMPORARY TABLE " . static::getTempTableName($leaderboard_source) . " (                
                created timestamp without time zone,
                updated timestamp without time zone,
                dailies bigint,
                wins bigint,
                id integer,
                players integer,
                character_id smallint,
                release_id smallint,
                mode_id smallint,
                multiplayer_type_id smallint,
                soundtrack_id smallint,
                daily_ranking_day_type_id smallint,
                date_id smallint,
                details jsonb
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
                character_id,
                release_id,
                mode_id,
                multiplayer_type_id,
                soundtrack_id,
                daily_ranking_day_type_id,
                date_id
            )
            SELECT 
                created,
                updated,
                id,
                character_id,
                release_id,
                mode_id,
                multiplayer_type_id,
                soundtrack_id,
                daily_ranking_day_type_id,
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
            UPDATE  " . static::getSchemaTableName($leaderboard_source) . " dr
            SET 
                dailies = drt.dailies,
                wins = drt.wins,
                players = drt.players,
                details = drt.details
            FROM " . static::getTempTableName($leaderboard_source) . " drt
            WHERE dr.id = drt.id
        ");
    }
    
    public static function getApiReadQuery(
        LeaderboardSources $leaderboard_source,
        Characters $character,
        Releases $release,
        Modes $mode,
        MultiplayerTypes $multiplayer_type,
        Soundtracks $soundtrack,
        DailyRankingDayTypes $daily_ranking_day_type
    ): Builder {
        return DB::table(static::getSchemaTableName($leaderboard_source) . ' AS dr')
            ->select([
                'd.name AS date',
                'dr.players',
                'dr.dailies',
                'dr.wins',
                'dr.details'
            ])
            ->join('dates AS d', 'd.id', '=', 'dr.date_id')
            ->where('dr.character_id', $character->id)
            ->where('dr.release_id', $release->id)
            ->where('dr.mode_id', $mode->id)
            ->where('dr.multiplayer_type_id', $multiplayer_type->id)
            ->where('dr.soundtrack_id', $soundtrack->id)
            ->where('dr.daily_ranking_day_type_id', $daily_ranking_day_type->id)
            ->orderBy('d.name', 'desc');
    }
}
