<?php

namespace App\Console\Commands\Leaderboards\Entries;

use DateTime;
use DateInterval;
use Illuminate\Console\Command;
use App\Components\CallbackHandler;
use App\Components\DateIncrementor;
use App\Jobs\Leaderboards\Entries\CacheDaily as CacheDailyJob;

class CacheRangeDaily extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaderboards:entries:daily:cache_range {start_date} {end_date}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Loads daily leaderboard entries into cache for the specified start_date and end_date.";

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
            CacheDailyJob::dispatch($date)->onConnection('sync');
        });
    
        $date_incrementor = new DateIncrementor(
            new DateTime($this->argument('start_date')), 
            new DateTime($this->argument('end_date')), 
            new DateInterval('P1M')
        );
        
        $date_incrementor->run($callback_handler);
    }
}
