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
                'name' => 'normal',
                'display_name' => 'Normal',
                'is_default' => 1,
                'sort_order' => 1
            ],
            [
                'name' => 'hard',
                'display_name' => 'Hard',
                'is_default' => 0,
                'sort_order' => 2
            ],
            [
                'name' => 'no_return',
                'display_name' => 'No Return',
                'is_default' => 0,
                'sort_order' => 3
            ],
            [
                'name' => 'phasing',
                'display_name' => 'Phasing',
                'is_default' => 0,
                'sort_order' => 4
            ],
            [
                'name' => 'randomizer',
                'display_name' => 'Randomizer',
                'is_default' => 0,
                'sort_order' => 5
            ],
            [
                'name' => 'mystery',
                'display_name' => 'Mystery',
                'is_default' => 0,
                'sort_order' => 6
            ]
        ]);
    }
}
