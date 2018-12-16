<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\LeaderboardSourceReleases;
use App\LeaderboardSources;
use App\Releases;

class LeaderboardSourceReleasesSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $leaderboard_sources = LeaderboardSources::getAllByName();
        $releases = Releases::getAllByName();
        
        
        /* ---------- Steam ----------*/
        
        $leaderboard_source_id = $leaderboard_sources['steam']->id;
        
        LeaderboardSourceReleases::insert([
            [
                'leaderboard_source_id' => $leaderboard_source_id,
                'release_id' => $releases['early_access']->release_id
            ],
            [
                'leaderboard_source_id' => $leaderboard_source_id,
                'release_id' => $releases['original']->release_id
            ],
            [
                'leaderboard_source_id' => $leaderboard_source_id,
                'release_id' => $releases['amplified_dlc_early_access']->release_id
            ],
            [
                'leaderboard_source_id' => $leaderboard_source_id,
                'release_id' => $releases['amplified_dlc']->release_id
            ]
        ]);
        
        
        /* ---------- Google Play ----------*/
        
        $leaderboard_source_id = $leaderboard_sources['google_play']->id;
        
        LeaderboardSourceReleases::insert([
            [
                'leaderboard_source_id' => $leaderboard_source_id,
                'release_id' => $releases['amplified_dlc']->release_id
            ]
        ]);
    }
}
