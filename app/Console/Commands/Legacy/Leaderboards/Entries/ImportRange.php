<?php

namespace App\Console\Commands\Legacy\Leaderboards\Entries;

use DateTime;
use App\Console\Commands\Date as Command;
use App\Jobs\Legacy\Leaderboards\Entries\Import as ImportJob;

class ImportRange extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'legacy:leaderboards:entries:import_range {--start_date=} {--end_date=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Imports leaderboard entries from all of the legacy site's cooresponding month partition tables for the specified start_date and end_date.";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
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
        
        
        /* ---------- Dispatch the job for each date in the date period ---------- */
        
        foreach($date_period as $date) {
            ImportJob::dispatch($date)->onConnection('sync');
        } 
    }
}
