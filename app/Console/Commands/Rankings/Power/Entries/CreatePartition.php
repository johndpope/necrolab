<?php

namespace App\Console\Commands\Rankings\Power\Entries;

use DateTime;
use Illuminate\Console\Command;
use App\Jobs\Rankings\Power\Entries\CreatePartition as CreatePartitionJob;

class CreatePartition extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rankings:power:entries:create_partition {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Creates the power ranking entries table partition for the specified date. Defaults to today's date when none is specified.";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        CreatePartitionJob::dispatch(new DateTime($this->argument('date')))->onConnection('sync');
    }
}
