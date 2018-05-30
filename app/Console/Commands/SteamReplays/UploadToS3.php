<?php

namespace App\Console\Commands\Leaderboards\Csv;

use DateTime;
use Illuminate\Console\Command;
use App\Jobs\SteamReplays\UploadToS3 as UploadToS3Job;

class UploadToS3 extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'steam_replays:s3:upload';

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
        UploadToS3Job::dispatch()->onConnection('sync');
    }
}
