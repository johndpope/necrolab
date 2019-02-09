<?php

namespace App\Console\Commands;

use DateTime;
use Illuminate\Console\Command;
use App\LeaderboardSources;
use App\Dates;

class DateRange extends Command {
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
        $leaderboard_source = LeaderboardSources::where('name', $this->option('leaderboard_source'))->firstOrFail();
    
        $start_date = new DateTime($this->option('start_date'));
        $end_date = new DateTime($this->option('end_date'));
        
        $dates = Dates::getValid($start_date, $end_date);
        
        foreach($dates as $date) {
            $this->job_class::dispatch($leaderboard_source, $date)->onConnection('sync');
        }
    }
}
