<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\LeaderboardSourceMultiplayerTypes;
use App\LeaderboardSources;
use App\MultiplayerTypes;

class LeaderboardSourceMultiplayerTypesSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $leaderboard_sources = LeaderboardSources::getAllByName();
        $multiplayer_types = MultiplayerTypes::getAllByName();
        
        
        /* ---------- Steam ----------*/
        
        $leaderboard_source_id = $leaderboard_sources['steam']->id;
        
        LeaderboardSourceMultiplayerTypes::insert([
            [
                'leaderboard_source_id' => $leaderboard_source_id,
                'multiplayer_type_id' => $multiplayer_types['single']->id
            ],
            [
                'leaderboard_source_id' => $leaderboard_source_id,
                'multiplayer_type_id' => $multiplayer_types['co_op']->id
            ]
        ]);
    }
}
