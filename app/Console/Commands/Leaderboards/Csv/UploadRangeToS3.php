<?php

namespace App\Console\Commands\Leaderboards\Csv;

use DateTime;
use DateInterval;
use Illuminate\Console\Command;
use App\Components\CallbackHandler;
use App\Components\DateIncrementor;
use App\Jobs\Leaderboards\Csv\UploadToS3 as UploadToS3Job;

class UploadRangeToS3 extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaderboards:csv:s3:upload_range {start_date} {end_date}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Uploads all leaderboard CSV data to S3 for the specified start_date and end_date.";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Uploads CSV data to S3 for the specified date.
     *
     * @param DateTime $date
     * @return mixed
     */
    public function uploadToS3(DateTime $date) {
        UploadToS3Job::dispatch($date)->onConnection('sync');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {        
        $callback_handler = new CallbackHandler();
        
        $callback_handler->setCallback([
            $this,
            'uploadToS3'
        ]);
    
        $date_incrementor = new DateIncrementor(new DateTime($this->argument('start_date')), new DateTime($this->argument('end_date')), new DateInterval('P1D'));
        
        $date_incrementor->run($callback_handler);
    }
}
