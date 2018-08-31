<?php

namespace App\Observers;

use App\Events\CachedModelUpdated;
use App\Modes;

class ModesObserver {
    /**
     * Handle to the "created" event for this model.
     *
     * @param  \App\Modes  $model
     * @return void
     */
    public function created(Modes $model) {
        event(new CachedModelUpdated($model));
    }

    /**
     * Handle to the "updated" event for this model.
     *
     * @param  \App\Modes  $model
     * @return void
     */
    public function updated(Modes $model) {
        event(new CachedModelUpdated($model));
    }

    /**
     * Handle to the "deleted" event for this model.
     *
     * @param  \App\Modes  $model
     * @return void
     */
    public function deleted(Modes $model) {
        event(new CachedModelUpdated($model));
    }
    
    /**
     * Handle to the "restored" event for this model.
     *
     * @param  \App\Modes  $model
     * @return void
     */
    public function restored(Modes $model) {
        event(new CachedModelUpdated($model));
    }
}
