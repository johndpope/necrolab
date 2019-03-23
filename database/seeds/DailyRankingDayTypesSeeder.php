<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\DailyRankingDayTypes;

class DailyRankingDayTypesSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        DailyRankingDayTypes::insert([
            [
                'name' => '30',
                'display_name' => '30 Days',
                'enabled' => 1,
                'sort_order' => 1,
                'is_default' => 1
            ],
            [
                'name' => '100',
                'display_name' => '100 Days',
                'enabled' => 1,
                'sort_order' => 2,
                'is_default' => 0
            ],
            [
                'name' => '0',
                'display_name' => 'All Time',
                'enabled' => 1,
                'sort_order' => 3,
                'is_default' => 0
            ]
        ]);
    }
}
