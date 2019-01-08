<?php

namespace App\Console\Commands\Steam\Leaderboards\Csv;

use DateTime;
use DateInterval;
use Illuminate\Console\Command;
use App\Components\CallbackHandler;
use App\Components\DateIncrementor;
use App\Components\DataManagers\Steam\Leaderboards\Csv as CsvManager;
use App\Jobs\Leaderboards\UploadToS3 as UploadToS3Job;

class UploadRangeToS3 extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'steam:leaderboards:csv:s3:upload_range {--start_date=} {--end_date=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Uploads all Steam leaderboard CSV data to S3 for the specified start_date and end_date.";

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
        $callback_handler = new CallbackHandler();
        
        $callback_handler->setCallback(function(DateTime $date) {
            $data_manager = new CsvManager($date);
        
            UploadToS3Job::dispatch($data_manager)->onConnection('sync');
        });
    
        $date_incrementor = new DateIncrementor(
            new DateTime($this->option('start_date')), 
            new DateTime($this->option('end_date')), 
            new DateInterval('P1D')
        );
        
        $date_incrementor->run($callback_handler);
    }
}
