<?php

namespace App\Console\Commands\Leaderboards;

use Illuminate\Console\Command;
use App\Jobs\Leaderboards\UpdateUrlNames as UpdateUrlNamesJob;

class UpdateUrlNames extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaderboards:url_names:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Updates the url_name field for all leaderboards.";

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
        UpdateUrlNamesJob::dispatch()->onConnection('sync');
    }
}
