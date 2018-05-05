<?php

namespace App\Console\Commands\Leaderboards\Csv;

use DateTime;
use Illuminate\Console\Command;
use App\Components\SteamLeaderboardDataManager\CsvManager;
use App\Jobs\Leaderboards\UploadToS3 as UploadToS3Job;

class UploadToS3 extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaderboards:csv:s3:upload {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Uploads leaderboard CSV data to S3 for the specified date. Defaults to today's date when none is specified.";

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
        $data_manager = new CsvManager(new DateTime($this->argument('date')));
    
        UploadToS3Job::dispatch($data_manager)->onConnection('sync');
    }
}
