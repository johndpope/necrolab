<?php

namespace App\Jobs\Leaderboards\Entries;

use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use App\Components\PostgresCursor;
use App\Components\Redis\Transaction\Pipeline as PipelineTransaction;
use App\Components\Encoder;
use App\Components\CacheNames\Leaderboards\Steam as CacheNames;
use App\LeaderboardEntries;
use App\ExternalSites;
use App\EntryIndexes;

class CacheDaily implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;
    
    protected $date;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(DateTime $date) {
        $this->date = $date;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        /* ---------- Retrieve all leaderboard entries ----------*/
    
        DB::beginTransaction();
        
        $cursor = new PostgresCursor(
            'leaderboard_entries_daily_cache', 
            LeaderboardEntries::getDailyCacheQuery($this->date),
            10000
        );
        
        
        /* ---------- Add each entry into its respective index ----------*/
        
        $indexes = [];
        
        foreach($cursor->getRecord() as $entry) {
            if(empty($indexes[$entry->daily_date])) {
                $indexes[$entry->daily_date] = [];
            }
            
            $users_index_base_name = CacheNames::getDailyIndex(new DateTime($entry->daily_date), [
                $entry->release_id,
                $entry->mode_id
            ]);
            
            ExternalSites::addToSiteIdIndexes($indexes[$entry->daily_date], $entry, $users_index_base_name, $entry->steam_user_id, $entry->rank);
        }
        
        
        /* ---------- Store all generated indexes in redis ----------*/
        
        EntryIndexes::createTemporaryTable();
        
        $entry_indexes_insert_queue = EntryIndexes::getTempInsertQueue(2000);
        
        if(!empty($indexes)) {
            foreach($indexes as $daily_date => $indexes_for_date) {
                foreach($indexes_for_date as $key => $index_data) {
                    ksort($index_data);
                
                    $entry_indexes_insert_queue->addRecord([
                        'data' => Encoder::encode($index_data),
                        'name' => $key,
                        'sub_name' => $daily_date
                    ]);
                }
            }
        }
        
        $entry_indexes_insert_queue->commit();
        
        EntryIndexes::saveTemp();
        
        DB::commit();
    }
}
