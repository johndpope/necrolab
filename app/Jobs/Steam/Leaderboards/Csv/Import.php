<?php

namespace App\Jobs\Steam\Leaderboards\Csv;

use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Storage;
use App\Components\LeaderboardNameGenerator;
use App\Components\DataManagers\Steam\Leaderboards\Csv as CsvManager;
use App\Components\QueueNames;
use App\Jobs\Leaderboards\UploadToS3;
use App\Jobs\Leaderboards\SaveToDatabase;
use App\Dates;

class Import implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;
    
    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 3600;
    
    /**
     * The date that leaderboard entries will be imported for.
     *
     * @var \App\Dates
     */
    protected $date;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Dates $date) {
        $this->date = $date;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        $leaderboard_name_generator = new LeaderboardNameGenerator(new DateTime($this->date->name));
        $steam_leaderboard_data_manager = new CsvManager($this->date);
        
        $leaderboard_names = array_merge($leaderboard_name_generator->getNonDailyNames(), $leaderboard_name_generator->getDailyNames());
        
        $steam_leaderboard_data_manager->deleteTemp();
        
        $steam_leaderboard_data_manager->saveTempNames($leaderboard_names);

        $steam_leaderboard_data_manager->runClientDownloader(
            config('steam.client_executable_path'),
            config('steam.appid'),
            config('steam.client_username'),
            config('steam.client_password'),
            $steam_leaderboard_data_manager->getTempNamesPath()
        );
        
        $steam_leaderboard_data_manager->compressTempToSaved();
        
        $steam_leaderboard_data_manager->deleteTemp();
        
        UploadToS3::dispatch($steam_leaderboard_data_manager)->onQueue(QueueNames::LEADERBOARDS);
        SaveToDatabase::dispatch($steam_leaderboard_data_manager)->onQueue(QueueNames::LEADERBOARDS);
    }
}
