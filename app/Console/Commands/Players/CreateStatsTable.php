<?php

namespace App\Console\Commands\Players;

use Illuminate\Console\Command;
use App\Jobs\Players\CreateStatsTable as CreateStatsTableJob;
use App\LeaderboardSources;

class CreateStatsTable extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'players:stats:create_table {--leaderboard_source=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Creates the player_stats table for the specified leaderboard source schema.";

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
        $leaderboard_source = LeaderboardSources::where('name', $this->option('leaderboard_source'))->firstOrFail();

        CreateStatsTableJob::dispatch($leaderboard_source)->onConnection('sync');
    }
}
