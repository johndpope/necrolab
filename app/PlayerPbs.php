<?php

namespace App;

use DateTime;
use stdClass;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use App\Components\PostgresCursor;
use App\Traits\IsSchemaTable;
use App\Traits\HasTempTable;
use App\Traits\HasManualSequence;
use App\Traits\AddsSqlCriteria;
use App\LeaderboardSources;
use App\Releases;
use App\Players;
use App\Leaderboards;
use App\LeaderboardSnapshots;
use App\LeaderboardEntryDetails;
use App\Replays;
use App\RunResults;
use App\ReplayVersions;
use App\Seeds;

class PlayerPbs extends Model {
    use IsSchemaTable, HasTempTable, HasManualSequence, AddsSqlCriteria;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'player_pbs';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    
    public static function getHighestZoneLevel(string $details): object {
        $details_split = explode('0000000', $details);
        
        $highest_zone_level = new stdClass();
        
        $highest_zone_level->highest_zone = (int)$details_split[0];
        $highest_zone_level->highest_level = (int)str_replace('0', '', $details_split[1]);
        
        return $highest_zone_level;
    } 
    
    public static function getIfWin(Releases $release, $zone, $level): int {    
        $is_win = 0;
        
        if($zone == $release->win_zone && $level == $release->win_level) {
            $is_win = 1;
        }
        
        return $is_win;
    }
    
    public static function getWinCount(int $score): int {
        $win_count = $score / 100;
        $win_count = round($win_count);
        
        return $win_count;
    }
    
    public static function getTime(int $score): ?float {
        $time = NULL;
    
        if(!empty($score)) {
            $time = (100000000 - $score) / 1000;
        }
        
        return $time;
    }
    
    public static function setPropertiesFromEntry(object $entry, object $leaderboard, DateTime $date): void {        
        // This logic path is for importing XML.
        if(!empty($entry->details)) {
            $highest_zone_level = static::getHighestZoneLevel($entry->details);
        
            if(!empty($highest_zone_level)) {
                $entry->zone = (int)$highest_zone_level->highest_zone;
                $entry->level = (int)$highest_zone_level->highest_level;
            }
        }
        
        $entry->is_win = 0;
        $entry->details = [];
        
        switch($leaderboard->leaderboard_type->name) {
            case 'score':
            case 'daily':
                $entry->details['score'] = (int)$entry->raw_score;
                
                $entry->is_win = static::getIfWin($leaderboard->release, $entry->zone, $entry->level);
                break;
            case 'speed':
                $entry->details['time'] = (float)static::getTime($entry->raw_score);
                
                $entry->is_win = 1;
                break;
            case 'deathless':
                $entry->details['win_count'] = static::getWinCount($entry->raw_score);
                
                $entry->is_win = 1;
                break;
        }
    }
    
    public static function isValid(object $leaderboard, $score): bool {
        $is_valid = false;
    
        switch($leaderboard->leaderboard_type->name) {
            case 'score':
            case 'daily':
                if($score <= env('MAX_VALID_SCORE')) {
                    $is_valid = true;
                }
                break;
            case 'speed':
            case 'deathless':
                $is_valid = true;
                break;
        }
    
        return $is_valid;
    }
    
    public static function createTemporaryTable(LeaderboardSources $leaderboard_source): void {
        DB::statement("
            CREATE TEMPORARY TABLE " . static::getTempTableName($leaderboard_source) . " (
                id integer,
                player_id integer,
                leaderboard_id integer,
                first_leaderboard_snapshot_id integer,
                first_rank integer,
                leaderboard_entry_details_id smallint,
                zone smallint,
                level smallint,
                is_win smallint,
                raw_score character varying(255),
                details jsonb
            )
            ON COMMIT DROP;
        ");
    }
    
    public static function saveNewTemp(LeaderboardSources $leaderboard_source): void {    
        DB::statement("
            INSERT INTO " . static::getSchemaTableName($leaderboard_source) . " (
                id,
                player_id,
                leaderboard_id,
                first_leaderboard_snapshot_id,
                first_rank,
                leaderboard_entry_details_id,
                zone,
                level,
                is_win,
                raw_score,
                details
            )
            SELECT 
                id,
                player_id,
                leaderboard_id,
                first_leaderboard_snapshot_id,
                first_rank,
                leaderboard_entry_details_id,
                zone,
                level,
                is_win,
                raw_score,
                details
            FROM " . static::getTempTableName($leaderboard_source) . "
        ");
    }
    
    public static function updateFromTemp(LeaderboardSources $leaderboard_source): void {}
    
    public static function addSelects(Builder $query): void {
        $query->addSelect([
            'led.name AS details',
            'sup.zone',
            'sup.level',
            'sup.is_win',
            'sup.raw_score',
            'sup.details',
            'sr.external_id AS replay_external_id',
            'se.name AS seed',
            'sr.downloaded',
            'sr.uploaded_to_s3',
            'srv.name AS version',
            'rr.name AS run_result',
            'ldc.name AS details_column',
            'dt.name AS details_column_data_type',
            'lt.show_seed',
            'lt.show_replay',
            'lt.show_zone_level'
        ]);
    }
    
    public static function addJoins(LeaderboardSources $leaderboard_source, Builder $query): void {
        $query->join(LeaderboardEntryDetails::getSchemaTableName($leaderboard_source) . " AS led", 'led.id', '=', 'sup.leaderboard_entry_details_id');
        $query->join('leaderboard_types AS lt', 'lt.id', '=', 'l.leaderboard_type_id');
        $query->join('leaderboard_details_columns AS ldc', 'ldc.id', '=', 'lt.leaderboard_details_column_id');
        $query->join('data_types AS dt', 'dt.id', '=', 'ldc.data_type_id');
    }
    
    public static function addLeftJoins(LeaderboardSources $leaderboard_source, Builder $query): void {    
        $query->leftJoin(Replays::getSchemaTableName($leaderboard_source) . ' AS sr', 'sr.player_pb_id', '=', 'sup.id');
        $query->leftJoin(RunResults::getSchemaTableName($leaderboard_source) . ' AS rr',  'rr.run_result_id', '=', 'sr.run_result_id');
        $query->leftJoin(ReplayVersions::getSchemaTableName($leaderboard_source) . ' AS srv', 'srv.id', '=', 'sr.replay_version_id');
        $query->leftJoin(Seeds::getSchemaTableName($leaderboard_source) . ' AS se', 'se.id', '=', 'sr.seed_id');
    }
    
    public static function getAllIdsByUnique(LeaderboardSources $leaderboard_source): array {        
        $query = DB::table(static::getSchemaTableName($leaderboard_source))->select([
            'id',
            'leaderboard_id',
            'player_id',
            'raw_score'
        ]);
        
        $cursor = new PostgresCursor(
            "{$leaderboard_source->name}_player_pb_ids_by_unique", 
            $query,
            20000
        );
        
        $all_ids_by_unique = [];
        
        foreach($cursor->getRecord() as $player_pb) {
            $all_ids_by_unique[$player_pb->leaderboard_id][$player_pb->player_id][$player_pb->raw_score] = $player_pb->id;
        }
        
        return $all_ids_by_unique;
    }
    
    public static function getPlayerApiReadQuery(
        string $player_id, 
        LeaderboardSources $leaderboard_source,
        int $character_id, 
        int $release_id, 
        int $mode_id,
        int $leaderboard_type_id,
        int $seeded_type_id, 
        int $multiplayer_type_id, 
        int $soundtrack_id
    ): Builder {
    
        $query = DB::table(static::getSchemaTableName($leaderboard_source) . ' AS sup')
            ->select([
                'l.leaderboard_id',                
                'ls.date AS first_snapshot_date',
                'sup.first_rank'
            ])
            ->join(Players::getSchemaTableName($leaderboard_source) . ' AS su', 'su.id', '=', 'sup.player_id')
            ->join(Leaderboards::getSchemaTableName($leaderboard_source) . ' AS l', 'l.id', '=', 'sup.leaderboard_id')
            ->join(LeaderboardSnapshots::getSchemaTableName($leaderboard_source) . ' AS ls', 'ls.id', '=', 'sup.first_leaderboard_snapshot_id')
            ->join('dates AS d', 'd.id', '=', 'ls.date_id');
        
        static::addSelects($query);
        static::addJoins($query);
        static::addLeftJoins($query);

        $query->where('su.external_id', $player_id)
            ->where('l.character_id', $character_id)
            ->where('l.release_id', $release_id)
            ->where('l.mode_id', $mode_id)
            ->where('l.leaderboard_type_id', $leaderboard_type_id)
            ->where('l.seeded_type_id', $seeded_type_id)
            ->where('l.multiplayer_type_id', $multiplayer_type_id)
            ->where('l.soundtrack_id', $soundtrack_id)
            ->orderBy('d.name', 'desc')
            ->orderBy('sup.player_pb_id', 'desc');
        
        return $query;
    }
}
