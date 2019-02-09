<?php

namespace App\Jobs\Rankings\Daily\Entries;

use DateTime;
use PDO;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use App\Components\PostgresCursor;
use App\Components\Encoder;
use App\LeaderboardSources;
use App\Dates;
use App\DailyRankings;
use App\DailyRankingEntries;
use App\LeaderboardTypes;

class UpdateStats implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;
    
    /**
     * The leaderboard source used to determine the schema to generate rankings on.
     *
     * @var \App\LeaderboardSources
     */
    protected $leaderboard_source;
    
    /**
     * The date that rankings will be generated for.
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
            'daily_rankings_stats_update', 
            DailyRankingEntries::getStatsReadQuery($this->leaderboard_source, $this->date),
            10000
        );
        
        $daily_ranking_ids = [];
        $players = [];
        $dailies = [];
        $wins = [];
        $details = [];
        
        foreach($cursor->getRecord() as $daily_ranking_entry) {
            $daily_ranking_id = $daily_ranking_entry->daily_ranking_id;
            
            $daily_ranking_ids[$daily_ranking_id] = $daily_ranking_id;
            
            if(!isset($players[$daily_ranking_id])) {
                $players[$daily_ranking_id] = 0;
                $dailies[$daily_ranking_id] = 0;
                $wins[$daily_ranking_id] = 0;
            }
        
            $players[$daily_ranking_id] += 1;
            $dailies[$daily_ranking_id] += $daily_ranking_entry->dailies;
            $wins[$daily_ranking_id] += $daily_ranking_entry->wins;
        
        
            /* ---------- Summarize category players ---------- */
            
            $decoded_details = Encoder::decode(stream_get_contents($daily_ranking_entry->details));

            if(!empty($decoded_details)) {
                foreach($decoded_details as $details_name => $details_value) {                
                    if(!isset($details[$daily_ranking_id][$details_name])) {
                        $details[$daily_ranking_id][$details_name] = 0;
                    }

                    $details[$daily_ranking_id][$details_name] += $details_value;
                }
            }
        }
        
        DailyRankings::createTemporaryTable($this->leaderboard_source);
        
        
        /* ---------- Configure the record queue and insert queue ---------- */

        $insert_queue = DailyRankings::getTempInsertQueue($this->leaderboard_source, 8000);
        
        
        /* ---------- Add all records into the record queue ---------- */
        
        if(!empty($daily_ranking_ids)) {
            foreach($daily_ranking_ids as $daily_ranking_id) {
                $insert_queue->addRecord([
                    'id' => $daily_ranking_id,
                    'players' => $players[$daily_ranking_id],
                    'dailies' => $dailies[$daily_ranking_id],
                    'wins' => $wins[$daily_ranking_id],
                    'details' => json_encode($details[$daily_ranking_id])
                ]);
            }
        }
        
        
        /* ---------- Save and commit ---------- */
        
        $insert_queue->commit();

        DailyRankings::updateFromTemp($this->leaderboard_source);
        
        DB::commit();
    }
}
