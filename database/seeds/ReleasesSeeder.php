<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Modes;

class ModesSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        Modes::insert([
            [
                'name' => 'amplified_dlc',
                'display_name' => 'AMPLIFIED DLC',
                'start_date' => '2017-07-12',
                'end_date' => NULL,
                'win_zone' => 5,
                'win_level' => 6,
                'is_default' => 1,
                'sort_order' => 1
            ],
            [
                'name' => 'amplified_dlc_early_access',
                'display_name' => 'AMPLIFIED DLC Early Access',
                'start_date' => '2017-01-24',
                'end_date' => '2017-07-11',
                'win_zone' => 5,
                'win_level' => 6,
                'is_default' => 0,
                'sort_order' => 2
            ],
            [
                'name' => 'original',
                'display_name' => 'Original',
                'start_date' => '2015-04-23',
                'end_date' => NULL,
                'win_zone' => 4,
                'win_level' => 6,
                'is_default' => 0,
                'sort_order' => 3
            ],
            [
                'name' => 'early_access',
                'display_name' => 'Early Access',
                'start_date' => '2014-07-30',
                'end_date' => '2015-04-22',
                'win_zone' => 3,
                'win_level' => 5,
                'is_default' => 0,
                'sort_order' => 4
            ],
            [
                'name' => 'alpha',
                'display_name' => 'Alpha',
                'start_date' => '2000-01-01',
                'end_date' => '2014-07-30',
                'win_zone' => NULL,
                'win_level' => NULL,
                'is_default' => 0,
                'sort_order' => 5
            ],
        ]);
    }
}
