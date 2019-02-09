<?php

namespace App\Console\Commands\Rankings\Daily\Entries;

use App\Console\Commands\CreatePartitions as Command;
use App\Jobs\Rankings\Daily\Entries\CreatePartition as CreatePartitionJob;

class CreatePartitions extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rankings:daily:entries:create_partitions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Creates daily ranking entries table partitions for each each leaderboard source and month between the specified start date and end date.";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        $this->job_class = CreatePartitionJob::class;
    
        parent::__construct();
    }
}
