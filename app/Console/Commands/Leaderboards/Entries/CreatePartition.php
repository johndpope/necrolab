<?php

namespace App\Console\Commands\Leaderboards\Entries;

use App\Console\Commands\CreatePartition as Command;
use App\Jobs\Leaderboards\Entries\CreatePartition as CreatePartitionJob;

class CreatePartition extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaderboards:entries:create_partition';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Creates the leaderboard entries table partition for each source of the specified date.";

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
