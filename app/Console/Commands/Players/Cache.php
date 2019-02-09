<?php

namespace App\Console\Commands\Players;

use Illuminate\Console\Command;
use App\Jobs\Players\Cache as CacheJob;
use App\LeaderboardSources;

class Cache extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'players:cache {--leaderboard_source=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Loads users of the specified leaderboard source into cache.";

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
    
        CacheJob::dispatch($leaderboard_source)->onConnection('sync');
    }
}
