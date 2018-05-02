<?php

namespace App\Console\Commands\Rankings\Power;

use DateTime;
use Illuminate\Console\Command;
use App\Jobs\Rankings\Power\Generate as GenerateJob;

class Generate extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rankings:power:generate {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Generates power rankings for the specified date. Defaults to today's date when none is specified.";

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
        GenerateJob::dispatch(new DateTime($this->argument('date')))->onConnection('sync');
    }
}
