<?php

namespace App\Console\Commands\Rankings\Daily\Entries;

use DateTime;
use Illuminate\Console\Command;
use App\Jobs\Rankings\Daily\Entries\UpdateStats as UpdateStatsJob;

class UpdateStats extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rankings:daily:entries:stats:update {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Updates aggregated stats of the entries for each daily ranking for the specified date.";

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
        UpdateStatsJob::dispatch(new DateTime($this->argument('date')))->onConnection('sync');
    }
}
