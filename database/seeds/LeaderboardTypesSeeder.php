<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\LeaderboardTypes;

class LeaderboardTypesSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        LeaderboardTypes::insert([
            [
                'name' => 'score',
                'display_name' => 'Score',
                'show_seed' => 1,
                'show_replay' => 1,
                'show_zone_level' => 1,
                'is_default' => 1,
                'sort_order' => 1
            ],
            [
                'name' => 'speed',
                'display_name' => 'Speed',
                'show_seed' => 1,
                'show_replay' => 1,
                'show_zone_level' => 0,
                'is_default' => 0,
                'sort_order' => 2
            ],
            [
                'name' => 'deathless',
                'display_name' => 'Deathless',
                'show_seed' => 0,
                'show_replay' => 0,
                'show_zone_level' => 1,
                'is_default' => 0,
                'sort_order' => 3
            ],
            [
                'name' => 'daily',
                'display_name' => 'Daily',
                'show_seed' => 1,
                'show_replay' => 0,
                'show_zone_level' => 1,
                'is_default' => 0,
                'sort_order' => 4
            ]
        ]);
    }
}
