<?php

namespace App\Console\Commands\Rankings\Daily\Entries;

use DateTime;
use DateInterval;
use Illuminate\Console\Command;
use App\Components\CallbackHandler;
use App\Components\DateIncrementor;
use App\Jobs\Rankings\Daily\Entries\CreatePartition as CreatePartitionJob;
use App\DailyRankingEntries;

class CreatePartitions extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rankings:daily:entries:create_partitions {--leaderboard_source=} {--start_date=} {--end_date=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Creates daily ranking entries table partitions for each each leaderboard source and month between the specified start date and end date.";

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
        $start_date = new DateTime($this->option('start_date'));
        $end_date = new DateTime($this->option('end_date'));
        
        DailyRankingEntries::dispatchRangePartitionCreationJob(CreatePartitionJob::class, $leaderboard_source_name, $start_date, $end_date);
    }
}
