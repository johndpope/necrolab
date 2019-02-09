<?php

namespace App\Console\Commands\Rankings\Power;

use App\Console\Commands\Date as Command;
use App\Jobs\Rankings\Power\Generate as GenerateJob;

class Generate extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rankings:power:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Generates power rankings for the specified date.";

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
