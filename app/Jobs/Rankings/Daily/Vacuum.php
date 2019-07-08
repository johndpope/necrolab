<?php

namespace App\Jobs\Rankings\Daily;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\LeaderboardSources;
use App\DailyRankings;

class Vacuum implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 3600;

    /**
     * The leaderboard source used to determine the schema of the daily_rankings table.
     *
     * @var \App\LeaderboardSources
     */
    protected $leaderboard_source;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(LeaderboardSources $leaderboard_source) {
        $this->leaderboard_source = $leaderboard_source;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        DailyRankings::getNewInstance()
            ->setSchema($this->leaderboard_source->name)
            ->vacuum([
                'full',
                'analyze'
            ]);
    }
}
