<?php

namespace App;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Components\RecordQueue;
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
        $record_queue = new RecordQueue($commit_count);
        
        $insert_queue = new InsertQueue("leaderboard_entries_temp");
        
        $insert_queue->addToRecordQueue($record_queue);
    
        return $record_queue;
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
    
    public static function getPowerRankingsQuery(DateTime $date) {
        return DB::table('leaderboard_snapshots ls')
            ->select([
                'lt.name AS leaderboard_type',
                'l.release_id',
                'l.mode_id',
                'l.is_seeded',
                'c.name AS character_name',
                'le.steam_user_pb_id',
                'sup.score',
                'sup.time',
                'sup.win_count',
                'sup.steam_user_id',
                'le.rank',
            ])
            ->join('leaderboards l', 'l.leaderboard_id', '=', 'ls.leaderboard_id')
            ->join('leaderboard_types lt', 'lt.leaderboard_type_id', '=', 'l.leaderboard_type_id')
            ->join('characters c', 'c.character_id', '=', 'l.character_id')
            ->join('leaderboard_ranking_types lrt', 'lrt.leaderboard_id', '=', 'l.leaderboard_id')
            ->join('ranking_types rt', 'rt.id', '=', 'lrt.ranking_type_id')
            ->join("leaderboard_entries_{$date->format('Y_m')} le", 'le.leaderboard_snapshot_id', '=', 'ls.leaderboard_snapshot_id')
            ->join("steam_user_pbs sup", 'sup.steam_user_pb_id', '=', 'le.steam_user_pb_id')
            ->where('ls.date', $date->format('Y-m-d'))
            ->where('rt.name', 'power');
    }
}
