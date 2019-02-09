<?php

namespace App\Console\Commands;

use DateTime;
use Illuminate\Console\Command;
use App\Dates;

class DataManagerDateRange extends Command {
    /**
     * The data manager class name.
     *
     * @var string
     */
     protected $data_manager_class;

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
        $this->signature .= " {--start_date=} {--end_date=}";
    
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $data_manager_class = $this->data_manager_class;
        
        $start_date = new DateTime($this->option('start_date'));
        $end_date = new DateTime($this->option('end_date'));
        
        $dates = Dates::getValid($start_date, $end_date);
        
        foreach($dates as $date) {
            $data_manager = new $data_manager_class($date);
        
            $this->job_class::dispatch($data_manager)->onConnection('sync');
        }
    }
}
