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

class CacheNonDaily implements ShouldQueue {
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
            'leaderboard_entries_non_daily_cache', 
            LeaderboardEntries::getNonDailyCacheQuery($this->date),
            100000
        );
        
        $indexes = [];
        
        
        /* ---------- Add each entry into its respective index ----------*/
        
        foreach($cursor->getRecord() as $entry) {            
            $users_index_base_name = CacheNames::getIndex((int)$entry->leaderboard_id, []);
                
            ExternalSites::addToSiteIdIndexes($indexes, $entry, $users_index_base_name, (int)$entry->steam_user_id, (int)$entry->rank);
        }
        
        
        /* ---------- Store all generated indexes in redis ----------*/
        
        EntryIndexes::createTemporaryTable();
        
        $entry_indexes_insert_queue = EntryIndexes::getTempInsertQueue(2000);
        
        $date_formatted = $this->date->format('Y-m-d');
        
        if(!empty($indexes)) {
            foreach($indexes as $key => $index_data) {
                ksort($index_data);
                
                $entry_indexes_insert_queue->addRecord([
                    'data' => Encoder::encode($index_data),
                    'name' => $key,
                    'date' => $date_formatted
                ]);
            }
        }
        
        $entry_indexes_insert_queue->commit();
        
        EntryIndexes::saveTemp();
        
        DB::commit();
    }
}