<?php

namespace App\Console\Commands\Leaderboards;

use InvalidArgumentException;
use Illuminate\Console\Command;
use App\Jobs\Leaderboards\CreateSourceSchema as CreateSourceSchemaJob;
use App\LeaderboardSources;

class CreateSourceSchema extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaderboards:sources:create_schema {--leaderboard_source=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Creates the schema for the specified leaderboard source.";

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
        $leaderboard_source = LeaderboardSources::getByName($leaderboard_source_name);
        
        if(empty($leaderboard_source)) {
            throw new InvalidArgumentException("Specified leaderboard_source '{$leaderboard_source_name}' is not a valid leaderboard source.");
        }
    
        CreateSourceSchemaJob::dispatch($leaderboard_source)->onConnection('sync');
    }
}
