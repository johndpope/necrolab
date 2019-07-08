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
        $schedule->call(function() {
            \App\Jobs\Dates\Add::dispatch(new DateTime('tomorrow'))->onQueue(QueueNames::DATES);
        })->daily();

        // Import Steam leaderboards CSV data
        $schedule->call(function() {
            $today_date = Dates::where('name', (new DateTime())->format('Y-m-d'))->first();

            \App\Jobs\Steam\Leaderboards\Csv\Import::dispatch($today_date)->onQueue(QueueNames::LEADERBOARDS);
        })
            ->everyThirtyMinutes()
            ->unlessBetween('01:30', '01:59');

        // Import Steam leaderboards XML data
        /*$schedule->call(function() {
            $today_date = Dates::where('name', (new DateTime())->format('Y-m-d'))->first();

            \App\Jobs\Steam\Leaderboards\Xml\Import::dispatch($today_date)->onQueue(QueueNames::LEADERBOARDS);
        })
            ->everyThirtyMinutes()
            ->unlessBetween('01:30', '01:59');*/

        // Create next month's leaderboard_entries, power_ranking_entries, and daily_ranking_entries partition tables
        $schedule->call(
            function() {
                $leaderboard_sources = LeaderboardSources::all();

                $next_month = new DateTime('next month');

                foreach($leaderboard_sources as $leaderboard_source) {
                    \App\Jobs\Leaderboards\Entries\CreatePartition::dispatch($leaderboard_source, $next_month)->onQueue(QueueNames::LEADERBOARDS);
                    \App\Jobs\Rankings\Power\Entries\CreatePartition::dispatch($leaderboard_source, $next_month)->onQueue(QueueNames::POWER_RANKINGS);
                    \App\Jobs\Rankings\Daily\Entries\CreatePartition::dispatch($leaderboard_source, $next_month)->onQueue(QueueNames::DAILY_RANKINGS);
                }
            }
        )->monthly();

        // Import Steam replay data
        $schedule->call(function() {
            \App\Jobs\Steam\Replays\Import::dispatch()->onQueue(QueueNames::REPLAYS);
        })
            ->everyFifteenMinutes()
            ->unlessBetween('01:30', '01:59');

        // Import Steam player data
        $schedule->call(function() {
            \App\Jobs\Steam\Players\Import::dispatch()->onQueue(QueueNames::PLAYERS);
        })->hourly();

        // Vacuum all tables that are updated throughout the previous day
        $schedule->call(function() {
            $yesterday_date = (new DateTime('yesterday'))->format('Y-m-d');

            $leaderboard_sources = LeaderboardSources::all();

            foreach($leaderboard_sources as $leaderboard_source) {
                \App\Jobs\EntryIndexes\Vacuum::dispatch($leaderboard_source)->onConnection('sync');
                \App\Jobs\Leaderboards\Vacuum::dispatch($leaderboard_source)->onConnection('sync');
                \App\Jobs\Leaderboards\VacuumSnapshots::dispatch($leaderboard_source)->onConnection('sync');
                \App\Jobs\Leaderboards\Entries\VacuumPartition::dispatch($leaderboard_source, $yesterday_date)->onConnection('sync');
                \App\Jobs\Pbs\Vacuum::dispatch($leaderboard_source)->onConnection('sync');
                \App\Jobs\Players\Vacuum::dispatch($leaderboard_source)->onConnection('sync');
                //\App\Jobs\Players\VacuumStats::dispatch($leaderboard_source)->onConnection('sync');
                \App\Jobs\Rankings\Daily\Vacuum::dispatch($leaderboard_source)->onConnection('sync');
                \App\Jobs\Rankings\Daily\Entries\VacuumPartition::dispatch($leaderboard_source, $yesterday_date)->onConnection('sync');
                \App\Jobs\Rankings\Power\Vacuum::dispatch($leaderboard_source)->onConnection('sync');
                \App\Jobs\Rankings\Power\Entries\VacuumPartition::dispatch($leaderboard_source, $yesterday_date)->onConnection('sync');
                \App\Jobs\Replays\Vacuum::dispatch($leaderboard_source)->onConnection('sync');
                \App\Jobs\Seeds\Vacuum::dispatch($leaderboard_source)->onConnection('sync');

            }
        })->dailyAt('01:30');
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
