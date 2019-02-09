<?php

namespace App\Console\Commands\Leaderboards\Entries;

use App\Console\Commands\Date as Command;
use App\Jobs\Leaderboards\Entries\CacheDaily as CacheDailyJob;

class CacheDaily extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaderboards:entries:daily:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Loads daily leaderboard entries into cache for the specified date.";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        $this->job_class = CacheDailyJob::class;
    
        parent::__construct();
    }
}
