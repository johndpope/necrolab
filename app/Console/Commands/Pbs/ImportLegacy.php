<?php

namespace App\Console\Commands\Pbs;

use App\Console\Commands\Date as Command;
use App\Jobs\Pbs\ImportLegacy as ImportLegacyJob;

class ImportLegacy extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pbs:legacy:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Imports player PBs from the legacy site.";

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
        ImportLegacyJob::dispatch()->onConnection('sync');
    }
}
