<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\LeaderboardSources;
use App\MultiplayerTypeMatches;
use App\MultiplayerTypes;

class MultiplayerTypeMatchesSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $leaderboard_source = LeaderboardSources::getByName('steam');
        $multiplayer_types = MultiplayerTypes::getAllByName();
        
        MultiplayerTypeMatches::insert([
            [
                'leaderboard_source_id' => $leaderboard_source->id,
                'multiplayer_type_id' => $multiplayer_types['co_op']->id,
                'match_regex' => '(co-op)',
                'sort_order' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ]
        ]);
    }
}
