<?php

namespace App\Console\Commands\Players;

use App\Console\Commands\Date as Command;
use App\Jobs\Players\UpdateStats as UpdateStatsJob;

class UpdateStats extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'players:stats:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Updates the stats for all users that have a PB on the specified date.";

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
