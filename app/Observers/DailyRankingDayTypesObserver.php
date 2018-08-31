<?php

namespace App\Observers;

use App\Events\CachedModelUpdated;
use App\DailyRankingDayTypes;

class DailyRankingDayTypesObserver {
    /**
     * Handle to the "created" event for this model.
     *
     * @param  \App\DailyRankingDayTypes  $model
     * @return void
     */
    public function created(DailyRankingDayTypes $model) {
        event(new CachedModelUpdated($model));
    }

    /**
     * Handle to the "updated" event for this model.
     *
     * @param  \App\DailyRankingDayTypes  $model
     * @return void
     */
    public function updated(DailyRankingDayTypes $model) {
        event(new CachedModelUpdated($model));
    }

    /**
     * Handle to the "deleted" event for this model.
     *
     * @param  \App\DailyRankingDayTypes  $model
     * @return void
     */
    public function deleted(DailyRankingDayTypes $model) {
        event(new CachedModelUpdated($model));
    }
    
    /**
     * Handle to the "restored" event for this model.
     *
     * @param  \App\DailyRankingDayTypes  $model
     * @return void
     */
    public function restored(DailyRankingDayTypes $model) {
        event(new CachedModelUpdated($model));
    }
}
