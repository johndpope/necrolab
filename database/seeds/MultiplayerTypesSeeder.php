<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\MultiplayerTypes;

class MultiplayerTypesSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        MultiplayerTypes::insert([
            [
                'name' => 'single',
                'display_name' => 'Single Player'
            ],
            [
                'name' => 'co_op',
                'display_name' => 'Co-op'
            ]
        ]);
    }
}