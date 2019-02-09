<?php

namespace App\Console\Commands\Leaderboards\Entries;

use App\Console\Commands\CreatePartitions as Command;
use App\Jobs\Leaderboards\Entries\CreatePartition as CreatePartitionJob;

class CreatePartitions extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaderboards:entries:create_partitions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Creates leaderboard entries table partitions for each leaderboard source and month between the specified start date and end date.";

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
