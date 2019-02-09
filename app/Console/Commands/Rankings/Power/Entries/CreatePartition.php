<?php

namespace App\Console\Commands\Rankings\Power\Entries;

use App\Console\Commands\CreatePartition as Command;
use App\Jobs\Rankings\Power\Entries\CreatePartition as CreatePartitionJob;

class CreatePartition extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rankings:power:entries:create_partition';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Creates the power ranking entries table partition for each source of the specified date.";

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
