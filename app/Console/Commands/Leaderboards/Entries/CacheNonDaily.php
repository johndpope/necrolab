<?php

namespace App\Console\Commands\Leaderboards\Entries;

use App\Console\Commands\Date as Command;
use App\Jobs\Leaderboards\Entries\CacheNonDaily as CacheNonDailyJob;

class CacheNonDaily extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaderboards:entries:non_daily:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Loads non daily leaderboard entries into cache for the specified date.";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        $this->job_class = CacheNonDailyJob::class;
    
        parent::__construct();
    }
}
