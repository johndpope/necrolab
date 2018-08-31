<?php

namespace App\Observers;

use App\Events\CachedModelUpdated;
use App\Characters;

class CharactersObserver {
    /**
     * Handle to the "created" event for this model.
     *
     * @param  \App\Characters  $model
     * @return void
     */
    public function created(Characters $model) {
        event(new CachedModelUpdated($model));
    }

    /**
     * Handle to the "updated" event for this model.
     *
     * @param  \App\Characters  $model
     * @return void
     */
    public function updated(Characters $model) {
        event(new CachedModelUpdated($model));
    }

    /**
     * Handle to the "deleted" event for this model.
     *
     * @param  \App\Characters  $model
     * @return void
     */
    public function deleted(Characters $model) {
        event(new CachedModelUpdated($model));
    }
    
    /**
     * Handle to the "restored" event for this model.
     *
     * @param  \App\Characters  $model
     * @return void
     */
    public function restored(Characters $model) {
        event(new CachedModelUpdated($model));
    }
}
