<?php

namespace App\Console\Commands\Rankings\Daily\Entries;

use App\Console\Commands\DateRange as Command;
use App\Jobs\Rankings\Daily\Entries\Cache as CacheJob;

class CacheRange extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rankings:daily:entries:cache_range';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Caches daily ranking entries for each date between the specified start date and end date.";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        $this->job_class = CacheJob::class;
    
        parent::__construct();
    }
}
