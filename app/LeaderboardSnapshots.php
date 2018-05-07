<?php

namespace App;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Components\RecordQueue;
use App\Components\InsertQueue;

class LeaderboardSnapshots extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'leaderboard_snapshots';
    
    /**
     * The primary key associated with the model.
     *
     * @var string
     */
    protected $primaryKey = 'leaderboard_snapshot_id';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    
    public static function createTemporaryTable() {    
        DB::statement("
            CREATE TEMPORARY TABLE leaderboard_snapshots_temp (
                leaderboard_snapshot_id integer,
                leaderboard_id integer,
                date date,
                created timestamp without time zone,
                updated timestamp without time zone
            )
            ON COMMIT DROP;
        ");
    }
    
    public static function getNewRecordId() {
        $new_record_id = DB::selectOne("
            SELECT nextval('leaderboard_snapshots_seq'::regclass) AS id
        ");
        
        return $new_record_id->id;
    }
    
    public static function getTempInsertQueue(int $commit_count) {        
        $record_queue = new RecordQueue($commit_count);
        
        $insert_queue = new InsertQueue("leaderboard_snapshots_temp");
        
        $insert_queue->addToRecordQueue($record_queue);
    
        return $record_queue;
    }
    
    public static function saveTemp() {
        DB::statement("
            INSERT INTO leaderboard_snapshots (
                leaderboard_snapshot_id, 
                leaderboard_id, 
                date, 
                created,
                updated
            )
            SELECT 
                leaderboard_snapshot_id,
                leaderboard_id,
                date,
                created,
                updated
            FROM leaderboard_snapshots_temp
            ON CONFLICT (leaderboard_snapshot_id) DO 
            UPDATE 
            SET 
                updated = excluded.updated
        ");
    }
    
    public static function getAllByLeaderboardIdForDate(DateTime $date) {
        $query = DB::table('leaderboard_snapshots')->where('date', $date->format('Y-m-d'));
        
        $snapshots_by_leaderboard_id = [];
        
        foreach($query->cursor() as $snapshot) {
            $snapshots_by_leaderboard_id[$snapshot->leaderboard_id] = $snapshot->leaderboard_snapshot_id;
        }
        
        return $snapshots_by_leaderboard_id;
    }
}
