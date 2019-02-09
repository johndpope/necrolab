<?php

namespace App\Console\Commands\Dates;

use DateTime;
use DateInterval;
use DatePeriod;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Jobs\Dates\Add as AddJob;

class AddRange extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dates:add_range {--start_date=} {--end_date=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Inserts a record into the dates table for each date between the specified start_date and end_date.";

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
        
        $end_date->modify('+1 day');
        
        $date_period = new DatePeriod(
            $start_date,
            new DateInterval('P1D'),
            $end_date
        );
        
        DB::beginTransaction();
        
        foreach($date_period as $date) {
            AddJob::dispatch($date)->onConnection('sync');
        }
        
        DB::commit();
    }
}
