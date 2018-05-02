<?php

namespace App\Console\Commands\Leaderboards\Xml;

use DateTime;
use Illuminate\Console\Command;
use App\Jobs\Leaderboards\Xml\UploadToS3 as UploadToS3Job;

class UploadToS3 extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaderboards:xml:s3:upload {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Uploads leaderboard XML data to S3 for the specified date. Defaults to today's date when none is specified.";

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
        UploadToS3Job::dispatch(new DateTime($this->argument('date')))->onConnection('sync');
    }
}
