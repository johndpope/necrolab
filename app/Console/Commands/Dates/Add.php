<?php

namespace App\Console\Commands\Dates;

use DateTime;
use Illuminate\Console\Command;
use App\Jobs\Dates\Add as AddJob;

class Add extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dates:add {--date=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Inserts a record into the dates table for the specified date.";

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
        AddJob::dispatch(new DateTime($this->option('date')))->onConnection('sync');
    }
}
