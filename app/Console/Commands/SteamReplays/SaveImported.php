<?php

namespace App\Console\Commands\SteamReplays;

use Illuminate\Console\Command;
use App\Jobs\SteamReplays\SaveImported as SaveImportedJob;

class SaveImported extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'steam_replays:save_imported';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Saves properties from imported steam replays into the database.";

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
        SaveImportedJob::dispatch()->onConnection('sync');
    }
}
