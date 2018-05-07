<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Components\RecordQueue;
use App\Components\InsertQueue;

class SteamReplays extends Model {
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
    
    public static function createTemporaryTable() {
        DB::statement("
            CREATE TEMPORARY TABLE steam_replays_temp (
                steam_user_pb_id integer,
                steam_user_id integer,
                ugcid numeric,
                downloaded smallint,
                invalid smallint,
                seed bigint,
                run_result_id smallint,
                steam_replay_version_id smallint,
                uploaded_to_s3 smallint
            )
            ON COMMIT DROP;
        ");
    }
    
    public static function getTempInsertQueue(int $commit_count) {        
        $record_queue = new RecordQueue($commit_count);
        
        $insert_queue = new InsertQueue("steam_replays_temp");
        
        $insert_queue->addToRecordQueue($record_queue);
    
        return $record_queue;
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
            FROM steam_replays_temp
        ");
    }
}
