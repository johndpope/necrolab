<?php

namespace App\Console\Commands\Players;

use App\Console\Commands\DateRange as Command;
use App\Jobs\Players\UpdateStats as UpdateStatsJob;

class UpdateStatsRange extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'players:stats:update_range';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Updates the stats for all users that have a PB for all dates between the specific start date and end date.";

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
