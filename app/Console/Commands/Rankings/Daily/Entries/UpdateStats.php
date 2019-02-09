<?php

namespace App\Console\Commands\Rankings\Daily\Entries;

use App\Console\Commands\Date as Command;
use App\Jobs\Rankings\Daily\Entries\UpdateStats as UpdateStatsJob;

class UpdateStats extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rankings:daily:entries:stats:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Updates aggregated stats of the entries for each daily ranking for the specified date.";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        $this->job_class = UpdateStatsJob::class;
    
        parent::__construct();
    }
}
