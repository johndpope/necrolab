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
        
        $leaderboard_type_id = $leaderboard_types['score']->id;
        
        LeaderboardTypeCharacters::insert([
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['cadence']->id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['bard']->id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['aria']->id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['bolt']->id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['monk']->id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['dove']->id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['eli']->id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['melody']->id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['dorian']->id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['coda']->id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['nocturna']->id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['diamond']->id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['mary']->id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['tempo']->id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['story']->id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['all']->id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['all_dlc']->id,
            ]
        ]);
    
    
        /* ---------- Speed ----------*/
        
        $leaderboard_type_id = $leaderboard_types['speed']->id;
        
        LeaderboardTypeCharacters::insert([
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['cadence']->id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['bard']->id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['aria']->id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['bolt']->id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['monk']->id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['dove']->id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['eli']->id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['melody']->id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['dorian']->id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['coda']->id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['nocturna']->id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['diamond']->id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['mary']->id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['tempo']->id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['story']->id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['all']->id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['all_dlc']->id,
            ]
        ]);
        
        
        /* ---------- Deathless ----------*/
        
        $leaderboard_type_id = $leaderboard_types['deathless']->id;
        
        LeaderboardTypeCharacters::insert([
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['cadence']->id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['bard']->id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['aria']->id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['bolt']->id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['monk']->id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['dove']->id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['eli']->id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['melody']->id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['dorian']->id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['coda']->id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['nocturna']->id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['diamond']->id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['mary']->id,
            ],
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['tempo']->id,
            ]
        ]);
        
        
        /* ---------- Daily ----------*/
        
        $leaderboard_type_id = $leaderboard_types['daily']->id;
        
        LeaderboardTypeCharacters::insert([
            [
                'leaderboard_type_id' => $leaderboard_type_id,
                'character_id' => $characters['cadence']->id
            ]
        ]);
    }
}
