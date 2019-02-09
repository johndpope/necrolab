<?php

namespace App\Components\DataManagers\Steam;

use Illuminate\Support\Facades\Storage;
use App\Components\DataManagers\Replays as BaseReplays;
use App\LeaderboardSources;

class Replays
extends BaseReplays {  
    public function __construct() {
        $leaderboard_source = LeaderboardSources::where('name', 'steam')->firstOrFail();
        
        parent::__construct($leaderboard_source);
    }
}
