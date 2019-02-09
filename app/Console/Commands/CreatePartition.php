<?php

namespace App\Console\Commands;

use DateTime;
use Illuminate\Console\Command;
use App\Console\Commands\Traits\WorksWithOneOrMoreLeaderboardSources;

class CreatePartition extends Command {
    use WorksWithOneOrMoreLeaderboardSources;

    /**
     * The job class being dispatched.
     *
     * @var string
     */
     protected $job_class;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        $this->signature .= " {--leaderboard_source=} {--date=}";
    
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $date = new DateTime($this->option('date'));
        
        $leaderboard_sources = $this->getLeaderboardSources();
        
        foreach($leaderboard_sources as $leaderboard_source) {
           $this->job_class::dispatch(
                $leaderboard_source,
                $date
            )->onConnection('sync');
        }
    }
}
