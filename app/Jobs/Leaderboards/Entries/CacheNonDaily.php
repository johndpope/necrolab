<?php

namespace App\Jobs\Leaderboards\Entries;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use App\Components\PostgresCursor;
use App\Components\Encoder;
use App\Components\CacheNames\Leaderboards as CacheNames;
use App\LeaderboardSources;
use App\Dates;
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
    
    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 3600;
    
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
        /* ---------- Retrieve all leaderboard entries ----------*/
    
        DB::beginTransaction();
        
        $cursor = new PostgresCursor(
            'leaderboard_entries_non_daily_cache', 
            LeaderboardEntries::getNonDailyCacheQuery($this->leaderboard_source, $this->date),
            100000
        );
        
        $indexes = [];
        
        
        /* ---------- Add each entry into its respective index ----------*/
        
        foreach($cursor->getRecord() as $entry) {            
            $users_index_base_name = CacheNames::getIndex($entry->leaderboard_id, []);
                
            ExternalSites::addToSiteIdIndexes($indexes, $entry, $users_index_base_name, (int)$entry->player_id, (int)$entry->rank);
        }
        
        
        /* ---------- Store all generated indexes in the entry_indexes table ----------*/
        
        EntryIndexes::createTemporaryTable($this->leaderboard_source);
        
        $entry_indexes_insert_queue = EntryIndexes::getTempInsertQueue($this->leaderboard_source, 2000);
        
        if(!empty($indexes)) {
            foreach($indexes as $key => $index_data) {
                ksort($index_data);
                
                $entry_indexes_insert_queue->addRecord([
                    'data' => Encoder::encode($index_data),
                    'name' => $key,
                    'sub_name' => $this->date->name
                ]);
            }
        }
        
        $entry_indexes_insert_queue->commit();
        
        EntryIndexes::saveNewTemp($this->leaderboard_source);
        
        DB::commit();
    }
}
