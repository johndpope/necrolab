<?php

namespace App\Console\Commands\Rankings\Daily;

use Illuminate\Console\Command;
use App\Console\Commands\Traits\WorksWithOneOrMoreLeaderboardSources;
use App\Jobs\Rankings\Daily\Vacuum as VacuumJob;

class Vacuum extends Command {
    use WorksWithOneOrMoreLeaderboardSources;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rankings:daily:vacuum {--leaderboard_source=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Vacuums the daily_rankings table for each leaderboard source schema.";

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
