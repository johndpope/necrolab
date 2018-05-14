<?php

namespace App;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Traits\HasPartitions;
use App\Traits\HasTempTable;

class LeaderboardEntries extends Model {
    use HasPartitions, HasTempTable;

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
            CREATE TEMPORARY TABLE " . static::getTempTableName() . " (
                leaderboard_snapshot_id integer NOT NULL,
                steam_user_pb_id integer NOT NULL,
                rank integer NOT NULL
            )
            ON COMMIT DROP;
        ");
    }
    
    public static function clear(DateTime $date) {    
        DB::delete("
            DELETE FROM " . static::getTableName($date) . " le
            USING  leaderboard_snapshots ls
            WHERE  le.leaderboard_snapshot_id = ls.leaderboard_snapshot_id
            AND    ls.date = :date
        ", [
            ':date' => $date->format('Y-m-d')
        ]);
    }
    
    public static function saveTemp(DateTime $date) {
        DB::statement("
            INSERT INTO " . static::getTableName($date) . " (
                leaderboard_snapshot_id,
                steam_user_pb_id,
                rank
            )
            SELECT 
                leaderboard_snapshot_id,
                steam_user_pb_id,
                rank
            FROM " . static::getTempTableName() . "
        ");
    } 
    
    public static function getPowerRankingsQuery(DateTime $date) {
        return DB::table('leaderboard_snapshots AS ls')
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
            ->join('leaderboards AS l', 'l.leaderboard_id', '=', 'ls.leaderboard_id')
            ->join('leaderboard_types AS lt', 'lt.leaderboard_type_id', '=', 'l.leaderboard_type_id')
            ->join('characters AS c', 'c.character_id', '=', 'l.character_id')
            ->join('leaderboard_ranking_types AS lrt', 'lrt.leaderboard_id', '=', 'l.leaderboard_id')
            ->join('ranking_types AS rt', 'rt.id', '=', 'lrt.ranking_type_id')
            ->join(static::getTableName($date) . " AS le", 'le.leaderboard_snapshot_id', '=', 'ls.leaderboard_snapshot_id')
            ->join("steam_user_pbs AS sup", 'sup.steam_user_pb_id', '=', 'le.steam_user_pb_id')
            ->where('ls.date', $date->format('Y-m-d'))
            ->where('rt.name', 'power');
    }
    
    public static function getDailyRankingsQuery(DateTime $start_date, DateTime $end_date) {
        $query = NULL;
        
        $table_names = static::getTableNames($start_date, $end_date);
        
        if(!empty($table_names)) {
            foreach($table_names as $table_name) {
                $partition_query = DB::table('leaderboard_snapshots AS ls')
                    ->select([
                        'l.release_id',
                        'l.mode_id',
                        'l.daily_date',
                        'sup.steam_user_id',
                        'le.rank',
                        'sup.is_win',
                        'sup.score'
                    ])
                    ->join('leaderboards AS l', 'l.leaderboard_id', '=', 'ls.leaderboard_id')
                    ->join('leaderboard_ranking_types AS lrt', 'lrt.leaderboard_id', '=', 'l.leaderboard_id')
                    ->join('ranking_types AS rt', 'rt.id', '=', 'lrt.ranking_type_id')
                    ->join("{$table_name} AS le", 'le.leaderboard_snapshot_id', '=', 'ls.leaderboard_snapshot_id')
                    ->join('steam_user_pbs AS sup', 'sup.steam_user_pb_id', '=', 'le.steam_user_pb_id')
                    ->whereBetween('ls.date', [
                        $start_date->format('Y-m-d'),
                        $end_date->format('Y-m-d')
                    ])
                    ->where('rt.name', 'daily');
                
                if(!isset($query)) {
                    $query = $partition_query;
                }
                else {
                    $query->unionAll($partition_query);
                }
            }
        }
            
        return $query;
    }
}
