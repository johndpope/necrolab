<?php

namespace App\Jobs\Players;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use App\Components\PostgresCursor;
use App\Components\Encoder;
use App\Components\CacheNames\Players as CacheNames;
use App\LeaderboardSources;
use App\Players;
use App\ExternalSites;
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
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(LeaderboardSources $leaderboard_source) {
        $this->leaderboard_source = $leaderboard_source;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {    
        DB::beginTransaction();
    
        $cursor = new PostgresCursor(
            'players_cache', 
            Players::getCacheQuery($this->leaderboard_source),
            20000
        );

        $users_index_base_name = CacheNames::getUsersIndex();
        
        $indexes = [];
        
        foreach($cursor->getRecord() as $player) {
            $player_id = (int)$player->id;
            
            ExternalSites::addToSiteIdIndexes($indexes, $player, $users_index_base_name, $player_id);
        }
        
        
        /* ---------- Setup for inserting into the entry_indexes table ----------*/
        
        EntryIndexes::createTemporaryTable($this->leaderboard_source);
        
        $entry_indexes_insert_queue = EntryIndexes::getTempInsertQueue($this->leaderboard_source, 2000);
        
        
        /* ---------- Store the player_id indexes for all sites ----------*/
        
        if(!empty($indexes)) {
            foreach($indexes as $key => $index_data) {                
                $entry_indexes_insert_queue->addRecord([
                    'data' => Encoder::encode($index_data),
                    'name' => $key,
                    'sub_name' => ''
                ]);
            }
        }
        
        $entry_indexes_insert_queue->commit();
        
        EntryIndexes::saveNewTemp($this->leaderboard_source);
        
        DB::commit();
    }
}
