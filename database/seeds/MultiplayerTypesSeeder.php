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
                'display_name' => 'Single Player',
                'is_default' => 1,
                'sort_order' => 1
            ],
            [
                'name' => 'co_op',
                'display_name' => 'Co-op',
                'is_default' => 0,
                'sort_order' => 2
            ]
        ]);
    }
}
