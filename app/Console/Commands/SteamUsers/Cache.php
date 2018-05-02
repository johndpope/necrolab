<?php

namespace App\Console\Commands\SteamUsers;

use DateTime;
use Illuminate\Console\Command;
use App\Jobs\SteamUsers\Cache as CacheJob;

class Cache extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'steam_users:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Loads steam users into cache.";

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
        CacheJob::dispatch()->onConnection('sync');
    }
}
