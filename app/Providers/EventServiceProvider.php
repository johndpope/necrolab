<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\CachedModelUpdated' => [
            'App\Listeners\RefreshModelCache',
        ],
        'SocialiteProviders\Manager\SocialiteWasCalled' => [
            'SocialiteProviders\\Discord\\DiscordExtendSocialite@handle',
            'SocialiteProviders\\Mixer\\MixerExtendSocialite@handle',
            'SocialiteProviders\\Steam\\SteamExtendSocialite@handle',
            'SocialiteProviders\\Patreon\\PatreonExtendSocialite@handle',
            'SocialiteProviders\\Twitch\\TwitchExtendSocialite@handle',
            'SocialiteProviders\\Twitter\\TwitterExtendSocialite@handle',
            'SocialiteProviders\\YouTube\\YouTubeExtendSocialite@handle'
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
