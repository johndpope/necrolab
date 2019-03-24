<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\RankingTypes;
use App\Leaderboards;

class RankingTypesSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        RankingTypes::insert([
            [
                'name' => 'power',
                'display_name' => 'Power'
            ],
            [
                'name' => 'daily',
                'display_name' => 'Daily'
            ],
            [
                'name' => 'super',
                'display_name' => 'Super'
            ]
        ]);
    }
}
