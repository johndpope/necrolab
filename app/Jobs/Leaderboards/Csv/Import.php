<?php

namespace App\Jobs\Leaderboards\Csv;

use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Storage;
use App\Components\LeaderboardNameGenerator;
use App\Components\SteamLeaderboardDataManager\CsvManager;
use App\Jobs\Leaderboards\Csv\UploadToS3;
use App\Jobs\Leaderboards\Csv\SaveToDatabase;

class Import implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;
    
    protected $date;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(DateTime $date) {
        $this->date = $date;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        $leaderboard_name_generator = new LeaderboardNameGenerator($this->date);
        $steam_leaderboard_data_manager = new CsvManager($this->date);
        
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
        
        UploadToS3::dispatch($this->date);
        SaveToDatabase::dispatch($this->date);
    }
}