<?php

namespace App\Console\Commands\Leaderboards\Xml;

use DateTime;
use DateInterval;
use Illuminate\Console\Command;
use App\Components\CallbackHandler;
use App\Components\DateIncrementor;
use App\Components\SteamDataManager\Leaderboards\Xml as XmlManager;
use App\Jobs\Leaderboards\SaveToDatabase as SaveToDatabaseJob;

class SaveRangeToDatabase extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaderboards:xml:database:save_range {start_date} {end_date}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Saves leaderboard XML data to the database for all dates between the specified start_date and end_date.";

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
            $data_manager = new XmlManager($date);
        
            SaveToDatabaseJob::dispatch($data_manager)->onConnection('sync');
        });
    
        $date_incrementor = new DateIncrementor(
            new DateTime($this->argument('start_date')), 
            new DateTime($this->argument('end_date')), 
            new DateInterval('P1D')
        );
        
        $date_incrementor->run($callback_handler);
    }
}