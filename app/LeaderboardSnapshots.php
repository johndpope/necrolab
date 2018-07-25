<?php

namespace App;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Components\PostgresCursor;
use App\Traits\HasTempTable;
use App\Traits\HasManualSequence;

class LeaderboardSnapshots extends Model {
    use HasTempTable, HasManualSequence;

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
        
        $cursor = new PostgresCursor(
            'leaderboard_snapshots_by_leaderboard', 
            $query,
            1000
        );
        
        $snapshots_by_leaderboard_id = [];
        
        foreach($cursor->getRecord() as $snapshot) {
            $snapshots_by_leaderboard_id[$snapshot->leaderboard_id] = $snapshot->leaderboard_snapshot_id;
        }
        
        return $snapshots_by_leaderboard_id;
    }
    
    public static function getApiReadQuery($lbid) {
        return DB::table('leaderboards AS l')
            ->select([
                'ls.date'
            ])
            ->join('leaderboard_snapshots AS ls', 'ls.leaderboard_id', '=', 'l.leaderboard_id')
            ->where('l.lbid', $lbid)
            ->orderBy('date', 'desc');
    }
}
