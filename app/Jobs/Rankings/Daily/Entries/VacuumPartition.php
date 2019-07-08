<?php

namespace App\Jobs\Rankings\Daily\Entries;

use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\LeaderboardSources;
use App\DailyRankingEntries;

class VacuumPartition implements ShouldQueue {
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
     * The leaderboard source used to determine the schema to generate rankings on.
     *
     * @var \App\LeaderboardSources
     */
    protected $leaderboard_source;

    /**
     * The date that rankings will be generated for.
     *
     * @var DateTime
     */
    protected $date;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(LeaderboardSources $leaderboard_source, DateTime $date) {
        $this->leaderboard_source = $leaderboard_source;

        $this->date = $date;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        DailyRankingEntries::getNewInstance()
            ->setSchema($this->leaderboard_source->name)
            ->setPartitionDate($this->date)
            ->vacuum([
                'full',
                'analyze'
            ]);
    }
}
