<?php

namespace App\Console\Commands\Rankings\Daily\Entries;

use DateTime;
use Illuminate\Console\Command;
use App\Jobs\Rankings\Daily\Entries\Cache as CacheJob;

class Cache extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rankings:daily:entries:cache {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Caches daily ranking entries for the specified date. Defaults to today's date when none is specified.";

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
        CacheJob::dispatch(new DateTime($this->argument('date')))->onConnection('sync');
    }
}
