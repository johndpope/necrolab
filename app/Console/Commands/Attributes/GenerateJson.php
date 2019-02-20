<?php

namespace App\Console\Commands\Attributes;

use DateTime;
use Illuminate\Console\Command;
use App\Jobs\Attributes\GenerateJson as GenerateJsonJob;

class GenerateJson extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attributes:generate_json';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Compiles all attributes (leaderboard_sources, characters, etc.) into one json file for use with web clients.";

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
        GenerateJsonJob::dispatch()->onConnection('sync');
    }
}
