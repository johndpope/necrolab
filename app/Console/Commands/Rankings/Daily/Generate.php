<?php

namespace App\Console\Commands\Rankings\Daily;

use App\Console\Commands\Date as Command;
use App\Jobs\Rankings\Daily\Generate as GenerateJob;

class Generate extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rankings:daily:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Generates daily rankings for the specified date.";

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
