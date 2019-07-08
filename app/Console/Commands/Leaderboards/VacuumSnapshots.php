<?php

namespace App\Console\Commands\Leaderboards;

use Illuminate\Console\Command;
use App\Console\Commands\Traits\WorksWithOneOrMoreLeaderboardSources;
use App\Jobs\Leaderboards\VacuumSnapshots as VacuumSnapshotsJob;

class VacuumSnapshots extends Command {
    use WorksWithOneOrMoreLeaderboardSources;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaderboards:vacuum_snapshots {--leaderboard_source=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Vacuums the leaderboard_snapshots table for each leaderboard source schema.";

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $leaderboard_sources = $this->getLeaderboardSources();

        foreach($leaderboard_sources as $leaderboard_source) {
            VacuumSnapshotsJob::dispatch($leaderboard_source)->onConnection('sync');
        }
    }
}
