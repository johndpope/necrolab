<?php

namespace App\Console\Commands\Leaderboards\Entries;

use App\Console\Commands\Date as Command;
use App\Jobs\Leaderboards\Entries\UpdateStats as UpdateStatsJob;

class UpdateStats extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaderboards:entries:stats:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Updates aggregated stats of the entries for each leaderboard for the specified date.";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        $this->job_class = UpdateStatsJob::class;
    
        parent::__construct();
    }
}
