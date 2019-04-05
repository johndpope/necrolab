<?php

namespace App\Console\Commands\Legacy\Leaderboards\Entries;

use DateTime;
use App\Console\Commands\Date as Command;
use App\Jobs\Legacy\Leaderboards\Entries\Import as ImportJob;

class Import extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'legacy:leaderboards:entries:import {--date=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Imports leaderboard entries from the legacy site's corresponding month partition table of the specified date.";

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
        ImportJob::dispatch(
            new DateTime($this->option('date'))
        )->onConnection('sync');
    }
}
