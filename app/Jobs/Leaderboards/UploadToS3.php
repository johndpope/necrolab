<?php

namespace App\Jobs\Leaderboards;

use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Storage;
use App\Components\DataManagers\Core as DataManager;

class UploadToS3 implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;
    
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
        $this->data_manager->copySavedToS3();
    }
}
