<?php

namespace App\Console\Commands\Cache;

use Illuminate\Console\Command;
use App\Jobs\Cache\ClearOpCache as ClearOpCacheJob;

class ClearOpCache extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:opcache:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Clears PHP's OpCache.";

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
        ClearOpCacheJob::dispatch()->onConnection('sync');
    }
}
