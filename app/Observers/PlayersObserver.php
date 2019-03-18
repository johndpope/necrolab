<?php

namespace App\Observers;

use App\LeaderboardSources;
use App\Players;

class PlayersObserver {
    /**
     * Handle to the "saved" event for this model.
     *
     * @param  \App\Players  $model
     * @return void
     */
    public function saved(Players $model) {
        $leaderboard_source = LeaderboardSources::getByName($model->getSchema());
    
        Players::updateRecordSearchIndex($leaderboard_source, $model);
    }
}
