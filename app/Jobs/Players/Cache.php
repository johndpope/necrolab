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
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {    
        DB::beginTransaction();
    
        $cursor = new PostgresCursor(
            'steam_users_cache', 
            Players::getCacheQuery(),
            20000
        );

        $users_index_base_name = CacheNames::getUsersIndex();
        
        $indexes = [];
        
        foreach($cursor->getRecord() as $steam_user) {
            $steam_user_id = (int)$steam_user->steam_user_id;
            
            ExternalSites::addToSiteIdIndexes($indexes, $steam_user, $users_index_base_name, $steam_user_id);
        }
        
        
        /* ---------- Setup for inserting into the entry_indexes table ----------*/
        
        EntryIndexes::createTemporaryTable();
        
        $entry_indexes_insert_queue = EntryIndexes::getTempInsertQueue(2000);
        
        
        /* ---------- Store the steam_user_id indexes for all sites ----------*/
        
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
        
        EntryIndexes::saveTemp();
        
        DB::commit();
    }
}
