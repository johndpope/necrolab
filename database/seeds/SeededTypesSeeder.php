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
                'display_name' => 'Unseeded'
            ],
            [
                'name' => 'seeded',
                'display_name' => 'Seeded'
            ]
        ]);
    }
}
