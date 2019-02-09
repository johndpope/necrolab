<?php

namespace App\Console\Commands\Steam\Leaderboards\Xml;

use App\Console\Commands\DataManagerDateRange as Command;
use App\Components\DataManagers\Steam\Leaderboards\Xml as XmlManager;
use App\Jobs\Leaderboards\UploadToS3 as UploadToS3Job;

class UploadRangeToS3 extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'steam:leaderboards:xml:s3:upload_range';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Uploads all Steam leaderboard XML data to S3 for the specified start_date and end_date.";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
       $this->data_manager_class = XmlManager::class;
        
        $this->job_class = UploadToS3Job::class;
    
        parent::__construct();
    }
}
