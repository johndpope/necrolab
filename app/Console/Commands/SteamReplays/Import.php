<?php

namespace App\Console\Commands\SteamReplays;

use Illuminate\Console\Command;
use App\Jobs\SteamReplays\Import as ImportJob;

class Import extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'steam_replays:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Imports Steam replays from Steam for all records in the database that have not been imported.";

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
