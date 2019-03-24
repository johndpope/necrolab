<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\SeededTypes;

class SeededTypesSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        SeededTypes::insert([
            [
                'name' => 'unseeded',
                'display_name' => 'Unseeded',
                'is_default' => 1,
                'sort_order' => 1
            ],
            [
                'name' => 'seeded',
                'display_name' => 'Seeded',
                'is_default' => 0,
                'sort_order' => 2
            ]
        ]);
    }
}
