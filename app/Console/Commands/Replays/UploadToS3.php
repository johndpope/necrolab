<?php

namespace App\Console\Commands\Replays;

use Illuminate\Console\Command;
use App\Jobs\Replays\UploadToS3 as UploadToS3Job;
use App\Components\DataManagers\Replays as DataManager;
use App\Console\Commands\Traits\WorksWithOneOrMoreLeaderboardSources;

class UploadToS3 extends Command {
    use WorksWithOneOrMoreLeaderboardSources;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'replays:s3:upload {--leaderboard_source=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Uploads all replays to S3 that have not been uploaded yet.";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $leaderboard_sources = $this->getLeaderboardSources();
    
        foreach($leaderboard_sources as $leaderboard_source) {
            $data_manager = new DataManager($leaderboard_source);
        
            UploadToS3Job::dispatch($data_manager)->onConnection('sync');
        }
    }
}
