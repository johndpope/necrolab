<?php

namespace App\Console\Commands;

use DateTime;
use DateInterval;
use DatePeriod;
use Illuminate\Console\Command;
use App\Jobs\Leaderboards\Entries\CreatePartition as CreatePartitionJob;
use App\Console\Commands\Traits\WorksWithOneOrMoreLeaderboardSources;

class CreatePartitions extends Command {
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
        $this->signature .= " {--leaderboard_source=} {--start_date=} {--end_date=}";
    
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $start_date = new DateTime($this->option('start_date'));
        $end_date = new DateTime($this->option('end_date'));
    
        $leaderboard_sources = $this->getLeaderboardSources();
        
        
        /* ---------- Setup the inclusive start and end dates ---------- */
        
        $month_start_date = new DateTime($start_date->format('Y-m-01'));
        
        $month_end_date = new DateTime($end_date->format('Y-m-01'));
        
        $month_end_date->modify('+1 day');
        
        
        /* ---------- Create the date period to loop through ---------- */
        
        $date_period = new DatePeriod(
            $month_start_date,
            new DateInterval('P1M'),
            $month_end_date
        );
        
        
        /* ---------- Dispatch the job for each date in the date period and leaderboard source ---------- */
        
        foreach($date_period as $date) {
            foreach($leaderboard_sources as $leaderboard_source) {
                $this->job_class::dispatch(
                    $leaderboard_source,
                    $date
                )->onConnection('sync');
            }
        }
    }
}
