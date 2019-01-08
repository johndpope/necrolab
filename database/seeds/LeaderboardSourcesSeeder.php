<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\LeaderboardSources;

class LeaderboardSourcesSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        LeaderboardSources::insert([
            [
                'name' => 'steam',
                'display_name' => 'Steam',
                'sort_order' => 1,
                'enabled' => 1
            ],
            [
                'name' => 'google_play',
                'display_name' => 'Google Play',
                'sort_order' => 2,
                'enabled' => 1
            ]
        ]);
    }
}