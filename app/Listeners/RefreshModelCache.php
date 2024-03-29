<?php

namespace App\Listeners;

use App\Events\CachedModelUpdated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class RefreshModelCache {
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * Handle the event.
     *
     * @param  CachedModelUpdated  $event
     * @return void
     */
    public function handle(CachedModelUpdated $event) {
        $event->model::refreshCache();
    }
}
