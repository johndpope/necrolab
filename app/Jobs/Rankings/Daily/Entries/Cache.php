<?php

namespace App\Jobs\Rankings\Daily\Entries;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use App\Jobs\Traits\WorksWithinDatabaseTransaction;
use App\Components\PostgresCursor;
use App\Components\Encoder;
use App\Components\CacheNames\Rankings\Daily as CacheNames;
use App\Components\CacheNames\Prefix as CacheNamesPrefix;
use App\LeaderboardSources;
use App\Dates;
use App\DailyRankingEntries;
use App\ExternalSites;
use App\EntryIndexes;

class Cache implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, WorksWithinDatabaseTransaction;
    
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
    protected function handleDatabaseTransaction(): void {
        /* ---------- Retrieve all daily ranking entries ----------*/
    
        DB::beginTransaction();
        
        $cursor = new PostgresCursor(
            'daily_ranking_entries_cache', 
            DailyRankingEntries::getCacheQuery($this->leaderboard_source, $this->date),
            10000
        );
        
        $indexes = [];
        $cache_names_prefix = new CacheNamesPrefix();
        
        /* ---------- Add each entry into its respective index ----------*/
        
        foreach($cursor->getRecord() as $entry) { 
            $player_id = (int)$entry->player_id;

            $cache_names_prefix->character_id = $entry->character_id;            
            $cache_names_prefix->release_id = $entry->release_id;
            $cache_names_prefix->mode_id = $entry->mode_id;
            $cache_names_prefix->multiplayer_type_id = $entry->multiplayer_type_id;
            $cache_names_prefix->soundtrack_id = $entry->soundtrack_id;
            $cache_names_prefix->daily_ranking_day_type_id = $entry->daily_ranking_day_type_id;
            
            
            /* ---------- Overall rank ---------- */
            
            ExternalSites::addToSiteIdIndexes(
                $indexes, 
                $entry, 
                CacheNames::getBase($cache_names_prefix), 
                $player_id, 
                (int)$entry->rank
            );
        }
        
        
        /* ---------- Store all generated indexes in redis ----------*/
        
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
