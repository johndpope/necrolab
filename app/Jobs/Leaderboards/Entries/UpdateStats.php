<?php

namespace App\Jobs\Leaderboards\Entries;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use App\Components\PostgresCursor;
use App\LeaderboardSources;
use App\Dates;
use App\LeaderboardEntries;
use App\LeaderboardSnapshots;

class UpdateStats implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;
    
    /**
     * The leaderboard source used to determine the schema to work within.
     *
     * @var \App\LeaderboardSources
     */
    protected $leaderboard_source;
    
    /**
     * The date that stats will be updated for.
     *
     * @var \App\Dates
     */
    protected $date;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(LeaderboardSources $leaderboard_source, Dates $date) {
        $this->leaderboard_source = $leaderboard_source;
    
        $this->date = $date;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {    
        DB::beginTransaction();
        
        $cursor = new PostgresCursor(
            'leaderboard_entries_stats_update', 
            LeaderboardEntries::getStatsReadQuery($this->leaderboard_source, $this->date),
            10000
        );
        
        $leaderboard_snapshot_ids = [];
        $players = [];
        $details = [];
        
        foreach($cursor->getRecord() as $leaderboard_entry) {
            $leaderboard_snapshot_id = $leaderboard_entry->leaderboard_snapshot_id;
            
            $leaderboard_snapshot_ids[$leaderboard_snapshot_id] = $leaderboard_snapshot_id;
            
            if(!isset($players[$leaderboard_snapshot_id])) {
                $players[$leaderboard_snapshot_id] = 0;
            }
        
            $players[$leaderboard_snapshot_id] += 1;
        
        
            /* ---------- Summarize details ---------- */
            
            $decoded_details = json_decode($leaderboard_entry->details, true);

            if(!empty($decoded_details)) {
                foreach($decoded_details as $details_name => $details_value) {                
                    if(!isset($details[$leaderboard_snapshot_id][$details_name])) {
                        $details[$leaderboard_snapshot_id][$details_name] = 0;
                    }

                    $details[$leaderboard_snapshot_id][$details_name] += $details_value;
                }
            }
        }
        
        LeaderboardSnapshots::createTemporaryTable($this->leaderboard_source);
        
        
        /* ---------- Configure the insert queue ---------- */

        $insert_queue = LeaderboardSnapshots::getTempInsertQueue($this->leaderboard_source, 20000);
        
        
        /* ---------- Add all records into the record queue ---------- */
        
        if(!empty($leaderboard_snapshot_ids)) {
            foreach($leaderboard_snapshot_ids as $leaderboard_snapshot_id) {
                $insert_queue->addRecord([
                    'id' => $leaderboard_snapshot_id,
                    'players' => $players[$leaderboard_snapshot_id],
                    'details' => json_encode($details[$leaderboard_snapshot_id])
                ]);
            }
        }
        
        
        /* ---------- Save and commit ---------- */
        
        $insert_queue->commit();

        LeaderboardSnapshots::updateFromTemp($this->leaderboard_source);
        
        DB::commit();
    }
}
