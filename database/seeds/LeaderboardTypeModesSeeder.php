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
        
        $leaderboard_type_id = $leaderboard_types['score']->leaderboard_type_id;
        
        LeaderboardTypeModes::insert([
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'mode_id' => $modes['normal']->mode_id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'mode_id' => $modes['hard']->mode_id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'mode_id' => $modes['no_return']->mode_id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'mode_id' => $modes['phasing']->mode_id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'mode_id' => $modes['randomizer']->mode_id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'mode_id' => $modes['mystery']->mode_id,
            ]
        ]);
    
    
        /* ---------- Speed ----------*/
        
        $leaderboard_type_id = $leaderboard_types['speed']->leaderboard_type_id;
        
        LeaderboardTypeModes::insert([
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'mode_id' => $modes['normal']->mode_id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'mode_id' => $modes['hard']->mode_id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'mode_id' => $modes['no_return']->mode_id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'mode_id' => $modes['phasing']->mode_id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'mode_id' => $modes['randomizer']->mode_id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'mode_id' => $modes['mystery']->mode_id,
            ]
        ]);
        
        
        /* ---------- Deathless ----------*/
        
        $leaderboard_type_id = $leaderboard_types['deathless']->leaderboard_type_id;
        
        LeaderboardTypeModes::insert([
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'mode_id' => $modes['normal']->mode_id,
            ]
        ]);
        
        
        /* ---------- Daily ----------*/
        
        $leaderboard_type_id = $leaderboard_types['daily']->leaderboard_type_id;
        
        LeaderboardTypeModes::insert([
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'mode_id' => $modes['normal']->mode_id,
            ]
        ]);
    }
}
