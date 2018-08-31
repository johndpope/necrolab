<?php

namespace App\Observers;

use App\Events\CachedModelUpdated;
use App\LeaderboardTypes;

class LeaderboardTypesObserver {
    /**
     * Handle to the "created" event for this model.
     *
     * @param  \App\LeaderboardTypes  $model
     * @return void
     */
    public function created(LeaderboardTypes $model) {
        event(new CachedModelUpdated($model));
    }

    /**
     * Handle to the "updated" event for this model.
     *
     * @param  \App\LeaderboardTypes  $model
     * @return void
     */
    public function updated(LeaderboardTypes $model) {
        event(new CachedModelUpdated($model));
    }

    /**
     * Handle to the "deleted" event for this model.
     *
     * @param  \App\LeaderboardTypes  $model
     * @return void
     */
    public function deleted(LeaderboardTypes $model) {
        event(new CachedModelUpdated($model));
    }
    
    /**
     * Handle to the "restored" event for this model.
     *
     * @param  \App\LeaderboardTypes  $model
     * @return void
     */
    public function restored(LeaderboardTypes $model) {
        event(new CachedModelUpdated($model));
    }
}
