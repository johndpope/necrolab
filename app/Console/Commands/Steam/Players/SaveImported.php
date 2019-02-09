<?php

namespace App\Console\Commands\Steam\Players;

use DateTime;
use Illuminate\Console\Command;
use App\Jobs\Steam\Players\SaveImported as SaveImportedJob;
use App\Components\DataManagers\Steam\Players as PlayersManager;

class SaveImported extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'steam:players:save_imported';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Saves imported Steam players to the database.";

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
        $data_manager = new PlayersManager(new DateTime());
    
        SaveImportedJob::dispatch($data_manager)->onConnection('sync');
    }
}
