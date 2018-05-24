<?php

namespace App;

use stdClass;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Traits\HasTempTable;
use App\Seeds;

class SteamReplays extends Model {
    use HasTempTable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'steam_replays';
    
    /**
     * The primary key associated with the model.
     *
     * @var string
     */
    protected $primaryKey = 'steam_replay_id';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    
    public static function getParsedReplayProperties(string $replay_file_data) {
        $parsed_replay_properties = NULL;
    
        $replay_file_split = explode('%*#%*', $replay_file_data);
        
        if(count($replay_file_split) == 2) {
            $parsed_replay_properties = new stdClass();
        
            $parsed_replay_properties->run_result = $replay_file_split[0];
            
            if(empty($parsed_replay_properties->run_result)) {
                $parsed_replay_properties->is_win = 1;
                $parsed_replay_properties->run_result = 'WIN';
            }
            else {
                $parsed_replay_properties->is_win = 0;
            }
        
            $replay_data = $replay_file_split[1];
            
            $replay_data_segments = explode('\\n', $replay_data);
            
            $parsed_replay_properties->version = $replay_data_segments[0];
            
            if($parsed_replay_properties->version < 82) {
                $zone_1_seed = $replay_data_segments[10];

                $parsed_replay_properties->seed = Seeds::getSeedFromZ1Seed($zone_1_seed);
            }
            elseif($parsed_replay_properties->version < 84) {
                $zone_1_seed = $replay_data_segments[10];

                $parsed_replay_properties->seed = Seeds::getOldDLCSeedFromZ1Seed($zone_1_seed);
            }
            else {
                $zone_1_seed = $replay_data_segments[7];

                $parsed_replay_properties->seed = Seeds::getDLCSeedFromZ1Seed($zone_1_seed);
            }
        }
        
        return $parsed_replay_properties;
    }
    
    public static function createTemporaryTable() {
        DB::statement("
            CREATE TEMPORARY TABLE " . static::getTempTableName() . " (
                steam_user_pb_id integer,
                steam_user_id integer,
                ugcid numeric,
                downloaded smallint,
                invalid smallint,
                seed bigint,
                run_result_id smallint,
                steam_replay_version_id smallint,
                seed_id bigint,
                uploaded_to_s3 smallint
            )
            ON COMMIT DROP;
        ");
    }
    
    public static function saveNewTemp() {
        DB::statement("
            INSERT INTO steam_replays (
                steam_user_pb_id,
                ugcid,
                steam_user_id,
                downloaded,
                invalid,
                uploaded_to_s3
            )
            SELECT 
                steam_user_pb_id,
                ugcid,
                steam_user_id,
                downloaded,
                invalid,
                uploaded_to_s3
            FROM " . static::getTempTableName() . "
        ");
    }
    
    public static function updateDownloadedFromTemp() {
        DB::update("
            UPDATE steam_replays sr
            SET 
                downloaded = srt.downloaded,
                invalid = srt.invalid,
                run_result_id = srt.run_result_id,
                steam_replay_version_id = srt.steam_replay_version_id,
                seed_id = srt.seed_id
            FROM " . static::getTempTableName() . " srt
            WHERE sr.steam_user_pb_id = srt.steam_user_pb_id
        ");
    }
    
    public static function getUnsavedReplaysQuery() {
        return static::where('downloaded', 0)->where('invalid', 0);
    }
}
