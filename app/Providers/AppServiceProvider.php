<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Register model event observers
        \App\DailyRankingDayTypes::observe(\App\Observers\DailyRankingDayTypesObserver::class);
        \App\Modes::observe(\App\Observers\ModesObserver::class);
        \App\LeaderboardTypes::observe(\App\Observers\LeaderboardTypesObserver::class);
        \App\Characters::observe(\App\Observers\CharactersObserver::class);
        \App\Releases::observe(\App\Observers\ReleasesObserver::class);
        \App\ExternalSites::observe(\App\Observers\ExternalSitesObserver::class);
        \App\SteamUsers::observe(\App\Observers\SteamUsersObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
