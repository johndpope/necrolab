<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\LeaderboardSources;
use App\ReleaseMatches;
use App\Releases;

class ReleaseMatchesSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $leaderboard_source = LeaderboardSources::getByName('steam');
        $releases = Releases::getAllByName();
        
        ReleaseMatches::insert([
            [
                'leaderboard_source_id' => $leaderboard_source->id,
                'release_id' => $releases['early_access']->id,
                'match_regex' => '.*(dev).*',
                'sort_order' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'leaderboard_source_id' => $leaderboard_source->id,
                'release_id' => $releases['amplified_dlc']->id,
                'match_regex' => '.*(?=.*prod.*)(?=.*dlc.*).*',
                'sort_order' => 2,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'leaderboard_source_id' => $leaderboard_source->id,
                'release_id' => $releases['original']->id,
                'match_regex' => '.*(prod).*',
                'sort_order' => 3,
                'created_at' => date('Y-m-d H:i:s')
            ],
        ]);
    }
}
