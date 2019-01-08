<?php

namespace App\Console\Commands\Steam\Leaderboards\Csv;

use DateTime;
use Illuminate\Console\Command;
use App\Jobs\Steam\Leaderboards\Csv\Import as ImportJob;

class Import extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'steam:leaderboards:csv:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Imports Steam leaderboard entries as CSV data from the Steam client for today's date.";

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
        ImportJob::dispatch(new DateTime())->onConnection('sync');
    }
}
