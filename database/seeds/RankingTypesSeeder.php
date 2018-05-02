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
    
        $ranking_types_by_name = RankingTypes::getAllByName();
        
        $leaderboards = Leaderboards::all();
        
        if(!empty($leaderboards)) {
            foreach($leaderboards as $leaderboard) {
                if($leaderboard->is_power_ranking == 1) {
                    DB::table('leaderboard_ranking_types')->insert([
                        'leaderboard_id' => $leaderboard->leaderboard_id,
                        'ranking_type_id' => $ranking_types_by_name['power']->id
                    ]);
                    
                    DB::table('leaderboard_ranking_types')->insert([
                        'leaderboard_id' => $leaderboard->leaderboard_id,
                        'ranking_type_id' => $ranking_types_by_name['super']->id
                    ]);
                }
                
                if($leaderboard->is_daily_ranking == 1) {
                    DB::table('leaderboard_ranking_types')->insert([
                        'leaderboard_id' => $leaderboard->leaderboard_id,
                        'ranking_type_id' => $ranking_types_by_name['daily']->id
                    ]);
                }
            }
        }
    }
}
