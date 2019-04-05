<?php

namespace App\Console\Commands\Legacy\Replays;

use App\Console\Commands\Date as Command;
use App\Jobs\Legacy\Replays\Import as ImportJob;

class Import extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'legacy:replays:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Imports replays from the legacy site.";

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
