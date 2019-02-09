<?php

namespace App\Console\Commands\Steam\Leaderboards\Csv;

use App\Console\Commands\DataManagerDateRange as Command;
use App\Components\DataManagers\Steam\Leaderboards\Csv as CsvManager;
use App\Jobs\Leaderboards\SaveToDatabase as SaveToDatabaseJob;

class SaveRangeToDatabase extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'steam:leaderboards:csv:database:save_range';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Saves Steam leaderboard CSV data to the database for all dates between the specified start_date and end_date.";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        $this->data_manager_class = CsvManager::class;
        
        $this->job_class = SaveToDatabaseJob::class;
    
        parent::__construct();
    }
}
