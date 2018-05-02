<?php

namespace App;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Components\InsertQueue;

class LeaderboardEntries extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'leaderboard_entries';
    
    /**
     * The primary key associated with the model.
     *
     * @var string
     */
    protected $primaryKey = [
        'leaderboard_snapshot_id',
        'steam_user_pb_id',
        'rank'
    ];
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    
    public static function createTemporaryTable() {
        DB::statement("
            CREATE TEMPORARY TABLE leaderboard_entries_temp (
                leaderboard_snapshot_id integer NOT NULL,
                steam_user_pb_id integer NOT NULL,
                rank integer NOT NULL
            )
            ON COMMIT DROP;
        ");
    }
    
    public static function getTempInsertQueue(int $commit_count) {
        return new InsertQueue("leaderboard_entries_temp", $commit_count);
    }
    
    public static function clear(DateTime $date) {
        $entries_table_name = "leaderboard_entries_{$date->format('Y_m')}";
    
        DB::delete("
            DELETE FROM {$entries_table_name} le
            USING  leaderboard_snapshots ls
            WHERE  le.leaderboard_snapshot_id = ls.leaderboard_snapshot_id
            AND    ls.date = :date
        ", [
            ':date' => $date->format('Y-m-d')
        ]);
    }
    
    public static function saveTemp(DateTime $date) {
        DB::statement("
            INSERT INTO leaderboard_entries_{$date->format('Y_m')} (
                leaderboard_snapshot_id,
                steam_user_pb_id,
                rank
            )
            SELECT 
                leaderboard_snapshot_id,
                steam_user_pb_id,
                rank
            FROM leaderboard_entries_temp
        ");
    } 
}
