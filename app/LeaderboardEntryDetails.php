<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Components\RecordQueue;
use App\Components\InsertQueue;
use App\Traits\GetByName;

class LeaderboardEntryDetails extends Model {
    use GetByName;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'leaderboard_entry_details';
    
    /**
     * The primary key associated with the model.
     *
     * @var string
     */
    protected $primaryKey = 'leaderboard_entry_details_id';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    
    public static function createTemporaryTable() {
        DB::statement("
            CREATE TEMPORARY TABLE leaderboard_entry_details_temp (
                leaderboard_entry_details_id smallint,
                \"name\" character varying(25)
            )
            ON COMMIT DROP;
        ");
    }
    
    public static function getNewRecordId() {
        $new_record_id = DB::selectOne("
            SELECT nextval('leaderboard_entry_details_seq'::regclass) AS id
        ");
        
        return $new_record_id->id;
    }
    
    public static function getTempInsertQueue(int $commit_count) {        
        $record_queue = new RecordQueue($commit_count);
        
        $insert_queue = new InsertQueue("leaderboard_entry_details_temp");
        
        $insert_queue->addToRecordQueue($record_queue);
    
        return $record_queue;
    }
    
    public static function saveTemp() {
        DB::statement("
            INSERT INTO leaderboard_entry_details (
                leaderboard_entry_details_id, 
                name
            )
            SELECT 
                leaderboard_entry_details_id, 
                name
            FROM leaderboard_entry_details_temp
        ");
    } 
}
