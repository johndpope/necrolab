<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\LeaderboardTypeCharacters;
use App\LeaderboardTypes;
use App\Characters;

class LeaderboardTypeCharactersSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $leaderboard_types = LeaderboardTypes::getAllByName();
        $characters = Characters::getAllByName();
        
        
        /* ---------- Score ----------*/
        
        $leaderboard_type_id = $leaderboard_types['score']->leaderboard_type_id;
        
        LeaderboardTypeCharacters::insert([
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['cadence']->character_id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['bard']->character_id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['aria']->character_id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['bolt']->character_id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['monk']->character_id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['dove']->character_id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['eli']->character_id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['melody']->character_id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['dorian']->character_id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['coda']->character_id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['nocturna']->character_id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['diamond']->character_id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['mary']->character_id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['tempo']->character_id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['story']->character_id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['all']->character_id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['all_dlc']->character_id,
            ]
        ]);
    
    
        /* ---------- Speed ----------*/
        
        $leaderboard_type_id = $leaderboard_types['speed']->leaderboard_type_id;
        
        LeaderboardTypeCharacters::insert([
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['cadence']->character_id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['bard']->character_id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['aria']->character_id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['bolt']->character_id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['monk']->character_id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['dove']->character_id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['eli']->character_id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['melody']->character_id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['dorian']->character_id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['coda']->character_id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['nocturna']->character_id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['diamond']->character_id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['mary']->character_id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['tempo']->character_id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['story']->character_id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['all']->character_id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['all_dlc']->character_id,
            ]
        ]);
        
        
        /* ---------- Deathless ----------*/
        
        $leaderboard_type_id = $leaderboard_types['deathless']->leaderboard_type_id;
        
        LeaderboardTypeCharacters::insert([
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['cadence']->character_id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['bard']->character_id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['aria']->character_id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['bolt']->character_id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['monk']->character_id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['dove']->character_id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['eli']->character_id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['melody']->character_id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['dorian']->character_id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['coda']->character_id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['nocturna']->character_id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['diamond']->character_id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['mary']->character_id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['tempo']->character_id,
            ]
        ]);
        
        
        /* ---------- Daily ----------*/
        
        $leaderboard_type_id = $leaderboard_types['daily']->leaderboard_type_id;
        
        LeaderboardTypeCharacters::insert([
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['cadence']->character_id,
            ]
        ]);
    }
}
