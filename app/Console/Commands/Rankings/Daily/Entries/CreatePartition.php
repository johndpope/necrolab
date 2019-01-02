<?php

namespace App\Console\Commands\Rankings\Daily\Entries;

use DateTime;
use Illuminate\Console\Command;
use App\Jobs\Rankings\Daily\Entries\CreatePartition as CreatePartitionJob;
use App\DailyRankingEntries;

class CreatePartition extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rankings:daily:entries:create_partition {--leaderboard_source=} {--date=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Creates the daily ranking entries table partition for each source of the specified date. Defaults to today's date when none is specified.";

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
        $leaderboard_source_name = $this->option('leaderboard_source');
        $date = new DateTime($this->option('date'));
        
        DailyRankingEntries::dispatchPartitionCreationJob(CreatePartitionJob::class, $leaderboard_source_name, $date);
    }
}
