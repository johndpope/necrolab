<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\LeaderboardSources;
use App\Dates;

class Date extends Command {
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
        $leaderboard_source = LeaderboardSources::where('name', $this->option('leaderboard_source'))->firstOrFail();
        
        $date = Dates::where('name', $this->option('date'))->firstOrFail();
        
        $this->job_class::dispatch($leaderboard_source, $date)->onConnection('sync');
    }
}
