<?php

namespace App;

use DateTime;
use stdClass;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use App\Components\PostgresCursor;
use App\Traits\HasTempTable;
use App\Traits\HasManualSequence;
use App\Traits\AddsSqlCriteria;
use App\Releases;

class PlayerPbs extends Model {
    use HasTempTable, HasManualSequence, AddsSqlCriteria;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'steam_user_pbs';
    
    /**
     * The primary key associated with the model.
     *
     * @var string
     */
    protected $primaryKey = 'steam_user_pb_id';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    
    public static function getHighestZoneLevel(string $details) {
        $details_split = explode('0000000', $details);
        
        $highest_zone_level = new stdClass();
        
        $highest_zone_level->highest_zone = (int)$details_split[0];
        $highest_zone_level->highest_level = (int)str_replace('0', '', $details_split[1]);
        
        return $highest_zone_level;
    } 
    
    public static function getIfWin(DateTime $date, Releases $release, $zone, $level) {    
        $is_win = 0;
        
        if($zone == $release->win_zone && $level == $release->win_level) {
            $is_win = 1;
        }
        
        return $is_win;
    }
    
    public static function getWinCount(int $score) {
        $win_count = NULL;
    
        if(!empty($score)) {
            $win_count = $score / 100;
            $win_count = round($win_count);
        }
        
        return $win_count;
    }
    
    public static function getTime(int $score) {
        $time = NULL;
    
        if(!empty($score)) {
            $time = (100000000 - $score) / 1000;
        }
        
        return $time;
    }
    
    public static function setPropertiesFromEntry(object $entry, object $leaderboard, DateTime $date) {
        $entry->time = NULL;
    
        if($leaderboard->leaderboard_type->name == 'speed') {            
            $entry->time = (float)static::getTime($entry->score);
        }
        
        //This logic path is for importing XML.
        if(!empty($entry->details)) {
            $highest_zone_level = static::getHighestZoneLevel($entry->details);
        
            if(!empty($highest_zone_level)) {
                $entry->zone = (int)$highest_zone_level->highest_zone;
                $entry->level = (int)$highest_zone_level->highest_level;
            }
        }
        
        $entry->is_win = 0;

        if(empty($leaderboard->leaderboard_type->name == 'score')) {
            $entry->is_win = static::getIfWin($date, $leaderboard->release, $entry->zone, $entry->level);
        }
        else {        
            $entry->is_win = 1;
        }
        
        $entry->win_count = NULL;
        
        if($leaderboard->leaderboard_type->name == 'deathless') {
            $entry->win_count = static::getWinCount($entry->score);
        }
    }
    
    public static function isValid(object $leaderboard, $score) {
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
    
    public static function createTemporaryTable() {
        DB::statement("
            CREATE TEMPORARY TABLE steam_user_pbs_temp (
                steam_user_pb_id integer NOT NULL,
                leaderboard_id smallint NOT NULL,
                steam_user_id integer NOT NULL,
                score integer,
                first_leaderboard_snapshot_id integer NOT NULL,
                first_rank integer NOT NULL,
                \"time\" double precision,
                win_count smallint,
                zone smallint,
                level smallint,
                is_win smallint,
                leaderboard_entry_details_id smallint NOT NULL
            )
            ON COMMIT DROP;
        ");
    }
    
    public static function saveNewTemp() {    
        DB::statement("
            INSERT INTO steam_user_pbs (
                steam_user_pb_id,
                leaderboard_id,
                steam_user_id,
                score,
                first_leaderboard_snapshot_id,
                first_rank,
                time,
                win_count,
                zone,
                level,
                is_win,
                leaderboard_entry_details_id
            )
            SELECT 
                steam_user_pb_id,
                leaderboard_id,
                steam_user_id,
                score,
                first_leaderboard_snapshot_id,
                first_rank,
                time,
                win_count,
                zone,
                level,
                is_win,
                leaderboard_entry_details_id
            FROM steam_user_pbs_temp
        ");
    }
    
    public static function addSelects(Builder $query) {
        $query->addSelect([
            'led.name AS details',
            'sup.zone',
            'sup.level',
            'sup.is_win',
            'sup.score',
            'sup.time',
            'sup.win_count',
            'sr.ugcid',
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
    
    public static function addJoins(Builder $query) {
        $query->join("leaderboard_entry_details AS led", 'led.id', '=', 'sup.leaderboard_entry_details_id');
        $query->join('leaderboard_types AS lt', 'lt.id', '=', 'l.leaderboard_type_id');
        $query->join('leaderboard_details_columns AS ldc', 'ldc.id', '=', 'lt.leaderboard_details_column_id');
        $query->join('data_types AS dt', 'dt.id', '=', 'ldc.data_type_id');
    }
    
    public static function addLeftJoins(Builder $query) {    
        $query->leftJoin('steam_replays AS sr', 'sr.steam_user_pb_id', '=', 'sup.steam_user_pb_id');
        $query->leftJoin('run_results AS rr',  'rr.run_result_id', '=', 'sr.run_result_id');
        $query->leftJoin('steam_replay_versions AS srv', 'srv.steam_replay_version_id', '=', 'sr.steam_replay_version_id');
        $query->leftJoin('seeds AS se', 'se.id', '=', 'sr.seed_id');
    }
    
    public static function getAllIdsByUnique() {        
        $query = DB::table('steam_user_pbs')->select([
            'steam_user_pb_id',
            'leaderboard_id',
            'steam_user_id',
            'score'
        ]);
        
        $cursor = new PostgresCursor(
            'steam_user_pbs_by_unique', 
            $query,
            20000
        );
        
        $all_ids_by_unique = [];
        
        foreach($cursor->getRecord() as $steam_user_pb) {
            $all_ids_by_unique[$steam_user_pb->leaderboard_id][$steam_user_pb->steam_user_id][$steam_user_pb->score] = $steam_user_pb->steam_user_pb_id;
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
    ) {
    
        $query = DB::table('steam_user_pbs AS sup')
            ->select([
                'l.lbid',                
                'ls.date AS first_snapshot_date',
                'sup.first_rank'
            ])
            ->join('steam_users AS su', 'su.steam_user_id', '=', 'sup.steam_user_id')
            ->join('leaderboards AS l', 'l.leaderboard_id', '=', 'sup.leaderboard_id')
            ->join('leaderboard_snapshots AS ls', 'ls.leaderboard_snapshot_id', '=', 'sup.first_leaderboard_snapshot_id');
        
        static::addSelects($query);
        static::addJoins($query);
        static::addLeftJoins($query);

        $query->where('su.steamid', $player_id)
            ->where('l.character_id', $character_id)
            ->where('l.release_id', $release_id)
            ->where('l.mode_id', $mode_id)
            ->where('l.leaderboard_type_id', $leaderboard_type_id)
            ->where('l.seeded_type_id', $seeded_type_id)
            ->where('l.multiplayer_type_id', $multiplayer_type_id)
            ->where('l.soundtrack_id', $soundtrack_id)
            ->orderBy('ls.date', 'desc')
            ->orderBy('sup.steam_user_pb_id', 'desc');
        
        return $query;
    }
}