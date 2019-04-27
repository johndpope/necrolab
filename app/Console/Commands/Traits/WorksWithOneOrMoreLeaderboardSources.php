<?php

namespace App\Console\Commands\Traits;

use Exception;
use Illuminate\Support\Collection;
use App\LeaderboardSources;

trait WorksWithOneOrMoreLeaderboardSources {
    protected function getLeaderboardSources(): Collection {
        $leaderboard_source_name = $this->option('leaderboard_source');
    
        $leaderboard_sources = [];
        
        if(!empty($leaderboard_source_name)) {
            $leaderboard_sources[] = LeaderboardSources::where('name', $leaderboard_source_name)->firstOrFail();
            
            $leaderboard_sources = collect($leaderboard_sources);
        }
        else {
            $leaderboard_sources = LeaderboardSources::where('enabled', 1)->get();
            
            if(empty($leaderboard_sources)) {
                throw new Exception("No leaderboard sources have been enabled.");
            }
        }
        
        return $leaderboard_sources;
    }
}
