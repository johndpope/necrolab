<?php

namespace App\Observers;

use App\Events\CachedModelUpdated;
use App\Releases;

class ReleasesObserver {
    /**
     * Handle to the "created" event for this model.
     *
     * @param  \App\Releases  $model
     * @return void
     */
    public function created(Releases $model) {
        event(new CachedModelUpdated($model));
    }

    /**
     * Handle to the "updated" event for this model.
     *
     * @param  \App\Releases  $model
     * @return void
     */
    public function updated(Releases $model) {
        event(new CachedModelUpdated($model));
    }

    /**
     * Handle to the "deleted" event for this model.
     *
     * @param  \App\Releases  $model
     * @return void
     */
    public function deleted(Releases $model) {
        event(new CachedModelUpdated($model));
    }
    
    /**
     * Handle to the "restored" event for this model.
     *
     * @param  \App\Releases  $model
     * @return void
     */
    public function restored(Releases $model) {
        event(new CachedModelUpdated($model));
    }
}
