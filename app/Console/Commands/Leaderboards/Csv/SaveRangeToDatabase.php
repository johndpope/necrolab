<?php

namespace App\Console\Commands\Leaderboards\Csv;

use DateTime;
use DateInterval;
use Illuminate\Console\Command;
use App\Components\CallbackHandler;
use App\Components\DateIncrementor;
use App\Jobs\Leaderboards\Csv\SaveToDatabase as SaveToDatabaseJob;

class SaveRangeToDatabase extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaderboards:csv:database:save_range {start_date} {end_date}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Saves leaderboard CSV data to the database for all dates between the specified start_date and end_date.";

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
            SaveToDatabaseJob::dispatch($date)->onConnection('sync');
        });
    
        $date_incrementor = new DateIncrementor(
            new DateTime($this->argument('start_date')), 
            new DateTime($this->argument('end_date')), 
            new DateInterval('P1D')
        );
        
        $date_incrementor->run($callback_handler);
    }
}
