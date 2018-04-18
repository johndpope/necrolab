<?php

namespace App\Console\Commands\Leaderboards\Csv;

use DateTime;
use Illuminate\Console\Command;
use App\Components\LeaderboardNameGenerator;
use App\Components\SteamLeaderboardDataManager\CsvManager;
use App\Jobs\Leaderboards\Csv\UploadToS3;
use App\Jobs\Leaderboards\Csv\SaveToDatabase;

class Import extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaderboards:csv:import {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports leaderboard entries as CSV data from the Steam client for the specified date.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Imports leaderboard entries CSV data for the specified date.
     *
     * @param DateTime $date
     * @return mixed
     */
    protected function importCsv(DateTime $date) {            
        $leaderboard_name_generator = new LeaderboardNameGenerator($date);
        $steam_leaderboard_data_manager = new CsvManager($date);
        
        $leaderboard_names = array_merge($leaderboard_name_generator->getNonDailyNames(), $leaderboard_name_generator->getDailyNames());
        
        $steam_leaderboard_data_manager->deleteTemp();
        
        $steam_leaderboard_data_manager->saveTempNames($leaderboard_names);

        $steam_leaderboard_data_manager->runClientDownloader(
            env('STEAM_CLIENT_EXECUTABLE_PATH'),
            env('STEAM_APPID'),
            env('STEAM_CLIENT_USERNAME'),
            env('STEAM_CLIENT_PASSWORD'),
            $steam_leaderboard_data_manager->getTempNamesPath()
        );
        
        $steam_leaderboard_data_manager->compressTempToSaved();
        
        $steam_leaderboard_data_manager->deleteTemp();
        
        UploadToS3::dispatch($date);
        SaveToDatabase::dispatch($date);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $this->importCsv(new DateTime($this->argument('date')));
    }
}
