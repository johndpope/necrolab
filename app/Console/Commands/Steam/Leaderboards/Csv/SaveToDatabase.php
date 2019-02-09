<?php

namespace App\Console\Commands\Steam\Leaderboards\Csv;

use App\Console\Commands\DataManagerDate as Command;
use App\Components\DataManagers\Steam\Leaderboards\Csv as CsvManager;
use App\Jobs\Leaderboards\SaveToDatabase as SaveToDatabaseJob;

class SaveToDatabase extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'steam:leaderboards:csv:database:save';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Saves Steam leaderboard CSV data to the database for the specified date.";

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
