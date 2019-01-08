<?php

namespace App\Console\Commands\Steam\Leaderboards\Xml;

use DateTime;
use Illuminate\Console\Command;
use App\Jobs\Steam\Leaderboards\Xml\Import as ImportJob;

class Import extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'steam:leaderboards:xml:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports leaderboard entries XML data from the Steam web API for the current date.';

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
