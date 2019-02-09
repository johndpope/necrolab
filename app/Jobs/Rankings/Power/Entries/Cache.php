<?php

namespace App\Jobs\Rankings\Power\Entries;

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
use App\Components\CacheNames\Rankings\Power as CacheNames;
use App\Components\CacheNames\Prefix as CacheNamesPrefix;
use App\LeaderboardSources;
use App\Dates;
use App\PowerRankingEntries;
use App\ExternalSites;
use App\Characters;
use App\EntryIndexes;

class Cache implements ShouldQueue {
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
        /* ---------- Retrieve all power ranking entries ----------*/
    
        DB::beginTransaction();
        
        $characters = Characters::getAllByName();
        
        $cursor = new PostgresCursor(
            'power_ranking_entries_cache', 
            PowerRankingEntries::getCacheQuery($this->leaderboard_source, $this->date),
            100000
        );
        
        $indexes = [];
        
        $cache_name_prefix = new CacheNamesPrefix();
        
        
        /* ---------- Add each entry into its respective index ----------*/

        foreach($cursor->getRecord() as $entry) {
            $player_id = (int)$entry->player_id;
            
            $cache_name_prefix->release_id = $entry->release_id;
            $cache_name_prefix->mode_id = $entry->mode_id;
            $cache_name_prefix->seeded_type_id = $entry->seeded_type_id;
            $cache_name_prefix->multiplayer_type_id = $entry->multiplayer_type_id;
            $cache_name_prefix->soundtrack_id = $entry->soundtrack_id;
            
            
            /* ---------- Overall rank ---------- */
            
            ExternalSites::addToSiteIdIndexes(
                $indexes, 
                $entry, 
                CacheNames::getBase($cache_name_prefix), 
                $player_id, 
                (int)$entry->rank
            );
            
            
            $category_ranks = Encoder::decode(stream_get_contents($entry->category_ranks));
            
            if(!empty($category_ranks)) {
                foreach($category_ranks as $leaderboard_type_name => $category_rank) {
                    ExternalSites::addToSiteIdIndexes(
                        $indexes, 
                        $entry, 
                        CacheNames::getCategory($cache_name_prefix, $leaderboard_type_name),
                        $player_id, 
                        (int)$category_rank
                    );
                }
            }
            
            
            /* ---------- Character ranks ---------- */
            
            $character_ranks = Encoder::decode(stream_get_contents($entry->characters));

            if(!empty($character_ranks)) {
                foreach($character_ranks as $character_name => $character_rank) {
                    ExternalSites::addToSiteIdIndexes(
                        $indexes, 
                        $entry, 
                        CacheNames::getCharacter($cache_name_prefix, $characters[$character_name]->id),
                        $player_id, 
                        (int)$character_rank['rank']
                    );
                }
            }
        }
        
        
        /* ---------- Store all generated indexes in entry_indexes ----------*/
        
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
