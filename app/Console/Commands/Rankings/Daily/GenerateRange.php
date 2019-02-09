<?php

namespace App\Console\Commands\Rankings\Daily;

use App\Console\Commands\DateRange as Command;
use App\Jobs\Rankings\Daily\Generate as GenerateJob;

class GenerateRange extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rankings:daily:generate_range';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Generates daily rankings for all dates between the specified start_date and end_date.";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        $this->job_class = GenerateJob::class;
    
        parent::__construct();
    }
}
