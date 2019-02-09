<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\LeaderboardTypeDetailsColumns;
use App\LeaderboardTypes;
use App\LeaderboardDetailsColumns;

class LeaderboardTypeDetailsColumnsSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $leaderboard_types = LeaderboardTypes::getAllByName();
        $leaderboard_details_columns = LeaderboardDetailsColumns::getAllByName();
        
        LeaderboardTypeDetailsColumns::insert([
            [
                'leaderboard_type_id' => $leaderboard_types['score']->id,
                'leaderboard_details_column_id' => $leaderboard_details_columns['score']->id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_types['speed']->id,
                'leaderboard_details_column_id' => $leaderboard_details_columns['time']->id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_types['deathless']->id,
                'leaderboard_details_column_id' => $leaderboard_details_columns['win_count']->id
            ],
            [
                'leaderboard_type_id' => $leaderboard_types['daily']->id,
                'leaderboard_details_column_id' => $leaderboard_details_columns['score']->id
            ]
        ]);
    }
}
