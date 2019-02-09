<?php

namespace App\Console\Commands\Steam\Leaderboards\Xml;

use App\Console\Commands\DataManagerDate as Command;
use App\Components\DataManagers\Steam\Leaderboards\Xml as XmlManager;
use App\Jobs\Leaderboards\SaveToDatabase as SaveToDatabaseJob;

class SaveToDatabase extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'steam:leaderboards:xml:database:save';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Saves Steam leaderboard XML data to the database for the specified date.";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        $this->data_manager_class = XmlManager::class;
        
        $this->job_class = SaveToDatabaseJob::class;
    
        parent::__construct();
    }
}
