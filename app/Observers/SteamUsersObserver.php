<?php

namespace App\Observers;

use App\SteamUsers;

class SteamUsersObserver {
    /**
     * Handle to the "saved" event for this model.
     *
     * @param  \App\SteamUsers  $model
     * @return void
     */
    public function saved(SteamUsers $model) {
        SteamUsers::updateRecordSearchIndex($model);
    }
}
