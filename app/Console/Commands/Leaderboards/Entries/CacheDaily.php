<?php

namespace App\Console\Commands\Leaderboards\Entries;

use DateTime;
use Illuminate\Console\Command;
use App\Jobs\Leaderboards\Entries\CacheDaily as CacheDailyJob;

class CacheDaily extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaderboards:entries:daily:cache {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Loads daily leaderboard entries into cache for the specified date. Defaults to today's date when none is specified.";

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
        CacheDailyJob::dispatch(new DateTime($this->argument('date')))->onConnection('sync');
    }
}
