<?php

namespace App\Jobs\PlayerPbs;

use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Components\Redis\Transaction\Pipeline as PipelineTransaction;
use App\Components\PostgresCursor;
use App\PlayerPbs;

class Cache implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;
    
    protected $redis;

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
        $this->redis = Redis::connection('default');
        
        $redis_transaction = new PipelineTransaction($this->redis, 1000);
    
        DB::beginTransaction();
    
        $cursor = new PostgresCursor(
            'cache_pbs_query', 
            PlayerPbs::getCacheQuery(),
            10000
        );
        
        foreach($cursor->getRecord() as $record) {
            $redis_transaction->lPush('steam_user_pbs', $record->steam_user_pb_id);
            
            $key = "steam_user_pbs:{$record->character_id}:{$record->release_id}:{$record->mode_id}:{$record->is_seeded}:{$record->is_co_op}:{$record->is_custom}";
            
            $redis_transaction->lPush($key, $record->steam_user_pb_id);
            
            $redis_transaction->hMSet("steam_user_pbs:{$record->steam_user_pb_id}", (array)$record);
        }
        
        DB::commit();
        
        $redis_transaction->commit();
    }
}
