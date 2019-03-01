<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\LeaderboardSources;
use App\SeededTypeMatches;
use App\SeededTypes;

class SeededTypeMatchesSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $leaderboard_source = LeaderboardSources::getByName('steam');
        $seeded_types = SeededTypes::getAllByName();
        
        SeededTypeMatches::insert([
            [
                'leaderboard_source_id' => $leaderboard_source->id,
                'seeded_type_id' => $seeded_types['seeded']->id,
                'match_regex' => '.*(seeded).*',
                'sort_order' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ]
        ]);
    }
}
