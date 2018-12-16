<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\DataTypes;

class DataTypesSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        DataTypes::insert([
            [
                'name' => 'seconds',
                'display_name' => 'Seconds',
            ],
            [
                'name' => 'integer',
                'display_name' => 'Integer'
            ]
        ]);
    }
}
