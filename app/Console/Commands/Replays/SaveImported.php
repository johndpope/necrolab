<?php

namespace App\Console\Commands\Replays;

use Illuminate\Console\Command;
use App\Jobs\Replays\SaveImported as SaveImportedJob;
use App\Components\DataManagers\Replays as DataManager;
use App\Console\Commands\Traits\WorksWithOneOrMoreLeaderboardSources;

class SaveImported extends Command {
    use WorksWithOneOrMoreLeaderboardSources;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'replays:save_imported {--leaderboard_source=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Saves properties from imported replays into the database.";

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
        $leaderboard_sources = $this->getLeaderboardSources();
    
        foreach($leaderboard_sources as $leaderboard_source) {
            $data_manager = new DataManager($leaderboard_source);
        
            SaveImportedJob::dispatch($data_manager)->onConnection('sync');
        }
    }
}
