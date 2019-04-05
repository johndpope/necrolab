<?php

namespace App\Console\Commands\Legacy\Players;

use App\Console\Commands\Date as Command;
use App\Jobs\Legacy\Players\Import as ImportJob;

class Import extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'legacy:players:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Imports players from the legacy site.";

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
        ImportJob::dispatch()->onConnection('sync');
    }
}
