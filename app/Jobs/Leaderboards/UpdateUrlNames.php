<?php

namespace App\Jobs\Leaderboards;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Components\PostgresCursor;
use App\Leaderboards;
use App\LeaderboardTypes;
use App\Characters;
use App\Releases;
use App\Modes;

class UpdateUrlNames implements ShouldQueue {
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
    
        Leaderboards::createTemporaryTable();
    
        $leaderboards_insert_queue = Leaderboards::getTempInsertQueue(5000);
    
        $cursor = new PostgresCursor(
            'leaderboards_update_url_names', 
            DB::table('leaderboards'),
            1000
        );
        
        foreach($cursor->getRecord() as $leaderboard) {
            $leaderboard->leaderboard_type = LeaderboardTypes::getById($leaderboard->leaderboard_type_id);
            $leaderboard->release = Releases::getById($leaderboard->release_id);
            $leaderboard->mode = Modes::getById($leaderboard->mode_id);
            $leaderboard->character = Characters::getById($leaderboard->character_id);
            
            $leaderboards_insert_queue->addRecord([
                'leaderboard_id' => $leaderboard->leaderboard_id,
                'url_name' => Leaderboards::generateUrlName($leaderboard),
            ]);
        }
        
        $leaderboards_insert_queue->commit();
        
        Leaderboards::updateUrlNamesFromTemp();
        
        DB::commit();
    }
}
