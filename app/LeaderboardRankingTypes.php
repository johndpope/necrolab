<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Components\RecordQueue;
use App\Components\InsertQueue;

class LeaderboardRankingTypes extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'leaderboard_ranking_types';
    
    /**
     * The primary key associated with the model.
     *
     * @var string
     */
    protected $primaryKey = [
        'leaderboard_id',
        'ranking_type_id'
    ];
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    
    public static function createTemporaryTable() {    
        DB::statement("
            CREATE TEMPORARY TABLE leaderboard_ranking_types_temp (
                leaderboard_id integer,
                ranking_type_id smallint
            )
            ON COMMIT DROP;
        ");
    }
    
    public static function getTempInsertQueue(int $commit_count) {        
        $record_queue = new RecordQueue($commit_count);
        
        $insert_queue = new InsertQueue("leaderboard_ranking_types_temp");
        
        $insert_queue->addToRecordQueue($record_queue);
    
        return $record_queue;
    }
    
    public static function saveTemp() {
        DB::statement("
            INSERT INTO leaderboard_ranking_types (
                leaderboard_id, 
                ranking_type_id
            )
            SELECT 
                leaderboard_id, 
                ranking_type_id
            FROM leaderboard_ranking_types_temp
        ");
    }
}