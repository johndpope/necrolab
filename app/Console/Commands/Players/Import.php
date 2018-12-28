<?php

namespace App\Console\Commands\Players;

use Illuminate\Console\Command;
use App\Jobs\Players\Import as ImportJob;

class Import extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'steam_users:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Imports Steam users from Steam.";

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
