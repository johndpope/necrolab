<?php

namespace App\Observers;

use App\Events\CachedModelUpdated;
use App\ExternalSites;

class ExternalSitesObserver {
    /**
     * Handle to the "created" event for this model.
     *
     * @param  \App\ExternalSites  $model
     * @return void
     */
    public function created(ExternalSites $model) {
        event(new CachedModelUpdated($model));
    }

    /**
     * Handle to the "updated" event for this model.
     *
     * @param  \App\ExternalSites  $model
     * @return void
     */
    public function updated(ExternalSites $model) {
        event(new CachedModelUpdated($model));
    }

    /**
     * Handle to the "deleted" event for this model.
     *
     * @param  \App\ExternalSites  $model
     * @return void
     */
    public function deleted(ExternalSites $model) {
        event(new CachedModelUpdated($model));
    }
    
    /**
     * Handle to the "restored" event for this model.
     *
     * @param  \App\ExternalSites  $model
     * @return void
     */
    public function restored(ExternalSites $model) {
        event(new CachedModelUpdated($model));
    }
}
