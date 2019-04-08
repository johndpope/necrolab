<?php

namespace App\Console;

use DateTime;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Components\QueueNames;
use App\Dates;
use App\LeaderboardSources;

class Kernel extends ConsoleKernel {
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule) {
        // Add tomorrow's date
        $schedule->job(
            new \App\Jobs\Dates\Add(new DateTime('tomorrow')), 
            QueueNames::DATES
        )->daily();
    
        $today_date = Dates::where('name', (new DateTime())->format('Y-m-d'))->firstOrFail();
        $leaderboard_sources = LeaderboardSources::all();
    
        // Import Steam leaderboards CSV data
        $schedule->job(
            new \App\Jobs\Steam\Leaderboards\Csv\Import($today_date), 
            QueueNames::LEADERBOARDS
        )->everyThirtyMinutes();
        
        // Import Steam leaderboards XML data
        $schedule->job(
            new \App\Jobs\Steam\Leaderboards\Xml\Import($today_date), 
            QueueNames::LEADERBOARDS
        )->everyThirtyMinutes();
        
        // Create next month's leaderboard_entries, power_ranking_entries, and daily_ranking_entries partition tables
        $schedule->job(
            function() use ($leaderboard_sources) {
                $next_month = new DateTime('next month');
            
                foreach($leaderboard_sources as $leaderboard_source) {
                    \App\Jobs\Leaderboards\Entries\CreatePartition::dispatch($leaderboard_source, $next_month)->onQueue(QueueNames::LEADERBOARDS);
                    \App\Jobs\Rankings\Power\Entries\CreatePartition::dispatch($leaderboard_source, $next_month)->onQueue(QueueNames::POWER_RANKINGS);
                    \App\Jobs\Rankings\Daily\Entries\CreatePartition::dispatch($leaderboard_source, $next_month)->onQueue(QueueNames::DAILY_RANKINGS);
                }
            }
        )->monthly();
        
        // Import Steam replay data
        $schedule->job(
            new \App\Jobs\Steam\Replays\Import(), 
            QueueNames::REPLAYS
        )->everyTenMinutes();
        
        // Import Steam player data
        $schedule->job(
            new \App\Jobs\Steam\Players\Import(), 
            QueueNames::PLAYERS
        )->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands() {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
