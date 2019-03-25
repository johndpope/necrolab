<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\LeaderboardTypeModes;
use App\LeaderboardTypes;
use App\Modes;

class LeaderboardTypeModesSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $leaderboard_types = LeaderboardTypes::getAllByName();
        $modes = Modes::getAllByName();
        
        
        /* ---------- Score ----------*/
        
        $leaderboard_type_id = $leaderboard_types['score']->id;
        
        LeaderboardTypeModes::insert([
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'mode_id' => $modes['normal']->id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'mode_id' => $modes['hard']->id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'mode_id' => $modes['no_return']->id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'mode_id' => $modes['phasing']->id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'mode_id' => $modes['randomizer']->id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'mode_id' => $modes['mystery']->id,
            ]
        ]);
    
    
        /* ---------- Speed ----------*/
        
        $leaderboard_type_id = $leaderboard_types['speed']->id;
        
        LeaderboardTypeModes::insert([
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'mode_id' => $modes['normal']->id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'mode_id' => $modes['hard']->id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'mode_id' => $modes['no_return']->id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'mode_id' => $modes['phasing']->id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'mode_id' => $modes['randomizer']->id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'mode_id' => $modes['mystery']->id,
            ]
        ]);
        
        
        /* ---------- Deathless ----------*/
        
        $leaderboard_type_id = $leaderboard_types['deathless']->id;
        
        LeaderboardTypeModes::insert([
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'mode_id' => $modes['normal']->id,
            ]
        ]);
        
        
        /* ---------- Daily ----------*/
        
        $leaderboard_type_id = $leaderboard_types['daily']->id;
        
        LeaderboardTypeModes::insert([
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'mode_id' => $modes['normal']->id,
            ]
        ]);
    }
}
