<?php

namespace App\Console\Commands\Leaderboards\Entries;

use App\Console\Commands\DateRange as Command;
use App\Jobs\Leaderboards\Entries\UpdateStats as UpdateStatsJob;

class UpdateStatsRange extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaderboards:entries:stats:update_range';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Updates aggregated stats of the leaderboard entries for the specified leaderboard source and date range.";

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
