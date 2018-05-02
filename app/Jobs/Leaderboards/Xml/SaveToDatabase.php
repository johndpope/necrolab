<?php

namespace App\Jobs\Leaderboards\Xml;

use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Components\SteamLeaderboardDataManager\XmlManager;

class SaveToDatabase implements ShouldQueue {
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
        $data_manager = new XmlManager($this->date);
        
        $data_manager->deleteTemp();
        
        $data_manager->decompressToTemp();
        
        $files = $data_manager->getTempFiles();
        
        if(!empty($files)) {
            foreach($files as $lbid => $file) {
                if($lbid != 'leaderboards') {
                    
                }
            }
        }
    }
}