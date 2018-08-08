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
        /* ---------- Retrieve all power ranking entries ----------*/
    
        DB::beginTransaction();
        
        $characters = Characters::getAllByName();
        
        $cursor = new PostgresCursor(
            'power_ranking_entries_cache', 
            PowerRankingEntries::getCacheQuery($this->date),
            100000
        );
        
        $indexes = [];
        
        
        /* ---------- Add each entry into its respective index ----------*/
        
        foreach($cursor->getRecord() as $entry) { 
            $steam_user_id = (int)$entry->steam_user_id;
            
            
            /* ---------- Overall rank ---------- */
            
            ExternalSites::addToSiteIdIndexes(
                $indexes, 
                $entry, 
                CacheNames::getBase($entry->release_id, $entry->mode_id, $entry->seeded), 
                $steam_user_id, 
                (int)$entry->rank
            );
            
            
            /* ---------- Score rank ---------- */
            
            if(!empty($entry->score_rank)) {
                ExternalSites::addToSiteIdIndexes(
                    $indexes, 
                    $entry, 
                    CacheNames::getScore($entry->release_id, $entry->mode_id, $entry->seeded),
                    $steam_user_id, 
                    (int)$entry->score_rank
                );
            }
            
            
            /* ---------- Speed rank ---------- */
            
            if(!empty($entry->speed_rank)) {
                ExternalSites::addToSiteIdIndexes(
                    $indexes, 
                    $entry, 
                    CacheNames::getSpeed($entry->release_id, $entry->mode_id, $entry->seeded),
                    $steam_user_id, 
                    (int)$entry->speed_rank
                );
            }
            
            
            /* ---------- Deathless rank ---------- */
            
            if(!empty($entry->deathless_rank)) {
                ExternalSites::addToSiteIdIndexes(
                    $indexes, 
                    $entry, 
                    CacheNames::getDeathless($entry->release_id, $entry->mode_id, $entry->seeded),
                    $steam_user_id, 
                    (int)$entry->deathless_rank
                );
            }
            
            
            /* ---------- Character ranks ---------- */
            
            $character_ranks = Encoder::decode(stream_get_contents($entry->characters));

            if(!empty($character_ranks)) {
                foreach($character_ranks as $character_name => $character_rank) {
                    ExternalSites::addToSiteIdIndexes(
                        $indexes, 
                        $entry, 
                        CacheNames::getCharacter($entry->release_id, $entry->mode_id, $entry->seeded, $characters[$character_name]->character_id),
                        $steam_user_id, 
                        (int)$character_rank['rank']
                    );
                }
            }
        }
        
        
        /* ---------- Store all generated indexes in entry_indexes ----------*/
        
        EntryIndexes::createTemporaryTable();
        
        $entry_indexes_insert_queue = EntryIndexes::getTempInsertQueue(2000);
        
        $date_formatted = $this->date->format('Y-m-d');
        
        if(!empty($indexes)) {
            foreach($indexes as $key => $index_data) {
                ksort($index_data);
                
                $entry_indexes_insert_queue->addRecord([
                    'data' => Encoder::encode($index_data),
                    'name' => $key,
                    'sub_name' => $date_formatted
                ]);
            }
        }
        
        $entry_indexes_insert_queue->commit();
        
        EntryIndexes::saveTemp();
        
        DB::commit();
    }
}
