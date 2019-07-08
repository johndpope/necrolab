<?php

namespace App\Console\Commands\Rankings\Daily\Entries;

use App\Console\Commands\CreatePartitions as Command;
use App\Jobs\Rankings\Daily\Entries\VacuumPartition as VacuumPartitionJob;

class CreatePartitions extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rankings:daily:entries:vacuum_partitions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Vacuums daily ranking entries table partitions for each leaderboard source and month between the specified start date and end date.";

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
