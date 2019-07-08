<?php

namespace App\Console\Commands\Rankings\Daily\Entries;

use App\Console\Commands\CreatePartition as Command;
use App\Jobs\Rankings\Daily\Entries\VacuumPartition as VacuumPartitionJob;

class VacuumPartition extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rankings:daily:entries:vacuum';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Vacuums the daily ranking entries table partition of each leaderboard source for the month and year of the specified date.";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        $this->job_class = VacuumPartitionJob::class;

        parent::__construct();
    }
}
