<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\LeaderboardSources;
use App\LeaderboardTypeMatches;
use App\LeaderboardTypes;

class LeaderboardTypeMatchesSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $leaderboard_source = LeaderboardSources::getByName('steam');
        $leaderboard_types = LeaderboardTypes::getAllByName();
        
        LeaderboardTypeMatches::insert([
            [
                'leaderboard_source_id' => $leaderboard_source->id,
                'leaderboard_type_id' => $leaderboard_types['speed']->id,
                'match_regex' => '(speedrun)',
                'sort_order' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'leaderboard_source_id' => $leaderboard_source->id,
                'leaderboard_type_id' => $leaderboard_types['deathless']->id,
                'match_regex' => '(deathless)',
                'sort_order' => 2,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'leaderboard_source_id' => $leaderboard_source->id,
                'leaderboard_type_id' => $leaderboard_types['score']->id,
                'match_regex' => '(hardcore|core|all\ zones)',
                'sort_order' => 3,
                'created_at' => date('Y-m-d H:i:s')
            ],
        ]);
    }
}
