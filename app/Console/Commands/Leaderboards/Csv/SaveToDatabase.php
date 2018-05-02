<?php

namespace App\Console\Commands\Leaderboards\Csv;

use DateTime;
use Illuminate\Console\Command;
use App\Jobs\Leaderboards\Csv\SaveToDatabase as SaveToDatabaseJob;

class SaveToDatabase extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaderboards:csv:database:save {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Saves leaderboard CSV data to the database for the specified date. Defaults to today's date when none is specified.";

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
        SaveToDatabaseJob::dispatch(new DateTime($this->argument('date')))->onConnection('sync');
    }
}
