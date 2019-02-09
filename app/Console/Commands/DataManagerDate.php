<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Dates;

class DataManagerDate extends Command {
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
        $this->signature .= " {--date=}";
    
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $date = Dates::where('name', $this->option('date'))->firstOrFail();
    
        $data_manager_class = $this->data_manager_class;
    
        $data_manager = new $data_manager_class($date);
        
        $this->job_class::dispatch($data_manager)->onConnection('sync');
    }
}
