<?php

namespace App;

use DatePeriod;
use DateTime;
use DateInterval;
use stdClass;
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
    
    public static function getApiReadQuery(int $lbid) {
        return DB::table('leaderboards AS l')
            ->select([
                'ls.date'
            ])
            ->join('leaderboard_snapshots AS ls', 'ls.leaderboard_id', '=', 'l.leaderboard_id')
            ->where('l.lbid', $lbid)
            ->orderBy('date', 'desc');
    }
    
    public static function getPlayerApiDates(string $steamid, int $lbid) {
        // Attempt to look up the earliest snapshot that this player has an entry for via their PBs.
        $earliest_snapshot = DB::table('steam_user_pbs AS sup')
            ->selectRaw("
                sup.leaderboard_id,
                l.release_id,
                MIN(ls.date) AS first_snapshot_date,
                r.end_date AS release_end_date
            ")
            ->join('steam_users AS su', 'su.steam_user_id', '=', 'sup.steam_user_id')
            ->join('leaderboards AS l', 'l.leaderboard_id', '=', 'sup.leaderboard_id')
            ->join('releases AS r', 'r.release_id', '=', 'l.release_id')
            ->join('leaderboard_snapshots AS ls', 'ls.leaderboard_snapshot_id', '=', 'sup.first_leaderboard_snapshot_id')
            ->where('su.steamid', $steamid)
            ->where('l.lbid', $lbid)
            ->groupBy(
                'sup.leaderboard_id',
                'l.release_id',
                'r.end_date'
            )
            ->first();
        
        $snapshot_dates = [];
        
        /*
            If a record is returned from the lookup generate the remaining dates 
            to either the end date of the release or today's date if the release is still active.
        */
        if(!empty($earliest_snapshot)) {
            $start_date = new DateTime($earliest_snapshot->first_snapshot_date);
            $end_date = new DateTime($earliest_snapshot->release_end_date);
            
            $snapshot_date_range = new DatePeriod(
                $start_date,
                new DateInterval('P1D'),
                $end_date
            );
            
            foreach($snapshot_date_range as $snapshot_date) {
                $record = new stdClass();
                
                $record->date = $snapshot_date->format('Y-m-d');
            
                $snapshot_dates[] = $record;
            }
            
            rsort($snapshot_dates);
        }
        
        return collect($snapshot_dates);
    }
}
