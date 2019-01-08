<?php

namespace App\Jobs\SteamReplays;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use App\Components\PostgresCursor;
use App\Components\DataManagers\Steam\Replays as SteamReplaysManager;
use App\SteamReplays;

class UploadToS3 implements ShouldQueue {
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
        /* ---------- Configure Data Manager ---------- */ 
        
        $data_manager = new SteamReplaysManager();
        
        $data_manager->deleteTemp();
        
        
        /* ---------- Database Setup ---------- */ 
        
        DB::beginTransaction();
        
        SteamReplays::createTemporaryTable();
        
        $insert_queue = SteamReplays::getTempInsertQueue(30000);
        
        
        /* ---------- Retrieve unuploaded records and add to record queue ---------- */ 
        
        $cursor = new PostgresCursor(
            'get_not_uploaded_replays', 
            SteamReplays::getNotS3UploadedQuery(),
            3000
        );
        
        foreach($cursor->getRecord() as $not_uploaded_record) {
            $data_manager->copySavedFileToS3($not_uploaded_record->ugcid);

            $insert_queue->addRecord([
                'steam_user_pb_id' => $not_uploaded_record->steam_user_pb_id,
                'uploaded_to_s3' => 1
            ]);
        }
        
        $insert_queue->commit();
        
        SteamReplays::updateS3UploadedFromTemp();
        
        DB::commit();
    }
}
