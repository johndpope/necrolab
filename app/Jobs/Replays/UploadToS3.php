<?php

namespace App\Jobs\Replays;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use App\Components\PostgresCursor;
use App\Components\DataManagers\Replays as DataManager;
use App\Replays;

class UploadToS3 implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;

    /**
     * The replays data manager used to interact with imported replay files.
     *
     * @var \App\Components\DataManagers\Replays
     */
    protected $data_manager;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(DataManager $data_manager) {
        $this->data_manager = $data_manager;
    }
    
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        $leaderboard_source = $this->data_manager->getLeaderboardSource();
    
        /* ---------- Database Setup ---------- */ 
        
        DB::beginTransaction();
        
        Replays::createTemporaryTable($leaderboard_source);
        
        $insert_queue = Replays::getTempInsertQueue($leaderboard_source, 30000);
        
        
        /* ---------- Retrieve unuploaded records and add to record queue ---------- */ 
        
        $cursor = new PostgresCursor(
            'get_not_uploaded_replays', 
            Replays::getNotS3UploadedQuery($leaderboard_source),
            3000
        );
        
        foreach($cursor->getRecord() as $not_uploaded_record) {
            $this->data_manager->copySavedFileToS3($not_uploaded_record->external_id);

            $insert_queue->addRecord([
                'player_pb_id' => $not_uploaded_record->player_pb_id,
                'uploaded_to_s3' => 1
            ]);
        }
        
        $insert_queue->commit();
        
        Replays::updateS3UploadedFromTemp($leaderboard_source);
        
        DB::commit();
    }
}
