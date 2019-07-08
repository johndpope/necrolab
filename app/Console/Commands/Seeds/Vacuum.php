<?php

namespace App\Console\Commands\Seeds;

use Illuminate\Console\Command;
use App\Console\Commands\Traits\WorksWithOneOrMoreLeaderboardSources;
use App\Jobs\Seeds\Vacuum as VacuumJob;

class Vacuum extends Command {
    use WorksWithOneOrMoreLeaderboardSources;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seeds:vacuum {--leaderboard_source=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Vacuums the seeds table for each leaderboard source schema.";

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $leaderboard_sources = $this->getLeaderboardSources();

        foreach($leaderboard_sources as $leaderboard_source) {
            VacuumJob::dispatch($leaderboard_source)->onConnection('sync');
        }
    }
}
