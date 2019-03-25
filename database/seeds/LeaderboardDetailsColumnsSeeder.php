<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\LeaderboardDetailsColumns;
use App\LeaderboardTypes;
use App\DataTypes;

class LeaderboardDetailsColumnsSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $data_types = DataTypes::getAllByName();
    
        LeaderboardDetailsColumns::insert([
            [
                'name' => 'score',
                'display_name' => 'Score',
                'data_type_id' => $data_types['integer']->id,
                'sort_order' => 1,
                'enabled' => 1
            ],
            [
                'name' => 'time',
                'display_name' => 'Time',
                'data_type_id' => $data_types['seconds']->id,
                'sort_order' => 2,
                'enabled' => 1
            ],
            [
                'name' => 'win_count',
                'display_name' => 'Wins',
                'data_type_id' => $data_types['integer']->id,
                'sort_order' => 3,
                'enabled' => 1
            ],
        ]);
    }
}
