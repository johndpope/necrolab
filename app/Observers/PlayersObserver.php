<?php

namespace App\Observers;

use App\Players;

class PlayersObserver {
    /**
     * Handle to the "saved" event for this model.
     *
     * @param  \App\Players  $model
     * @return void
     */
    public function saved(Players $model) {
        Players::updateRecordSearchIndex($model);
    }
}
