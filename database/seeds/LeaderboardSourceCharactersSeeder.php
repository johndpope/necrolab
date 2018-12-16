<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\LeaderboardSourceCharacters;
use App\LeaderboardSources;
use App\Characters;

class LeaderboardSourceCharactersSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $leaderboard_sources = LeaderboardSources::getAllByName();
        $characters = Characters::getAllByName();
        
        
        /* ---------- Steam ----------*/
        
        $leaderboard_source_id = $leaderboard_sources['steam']->id;
        
        LeaderboardSourceCharacters::insert([
            [
                'leaderboard_source_id' => $leaderboard_source_id,
                'character_id' => $characters['cadence']->character_id,
            ],
            [
                'leaderboard_source_id' => $leaderboard_source_id,
                'character_id' => $characters['bard']->character_id,
            ],
            [
                'leaderboard_source_id' => $leaderboard_source_id,
                'character_id' => $characters['aria']->character_id,
            ],
            [
                'leaderboard_source_id' => $leaderboard_source_id,
                'character_id' => $characters['bolt']->character_id,
            ],
            [
                'leaderboard_source_id' => $leaderboard_source_id,
                'character_id' => $characters['monk']->character_id,
            ],
            [
                'leaderboard_source_id' => $leaderboard_source_id,
                'character_id' => $characters['dove']->character_id,
            ],
            [
                'leaderboard_source_id' => $leaderboard_source_id,
                'character_id' => $characters['eli']->character_id,
            ],
            [
                'leaderboard_source_id' => $leaderboard_source_id,
                'character_id' => $characters['melody']->character_id,
            ],
            [
                'leaderboard_source_id' => $leaderboard_source_id,
                'character_id' => $characters['dorian']->character_id,
            ],
            [
                'leaderboard_source_id' => $leaderboard_source_id,
                'character_id' => $characters['coda']->character_id,
            ],
            [
                'leaderboard_source_id' => $leaderboard_source_id,
                'character_id' => $characters['nocturna']->character_id,
            ],
            [
                'leaderboard_source_id' => $leaderboard_source_id,
                'character_id' => $characters['diamond']->character_id,
            ],
            [
                'leaderboard_source_id' => $leaderboard_source_id,
                'character_id' => $characters['mary']->character_id,
            ],
            [
                'leaderboard_source_id' => $leaderboard_source_id,
                'character_id' => $characters['tempo']->character_id,
            ],
            [
                'leaderboard_source_id' => $leaderboard_source_id,
                'character_id' => $characters['story']->character_id,
            ],
            [
                'leaderboard_source_id' => $leaderboard_source_id,
                'character_id' => $characters['all']->character_id,
            ],
            [
                'leaderboard_source_id' => $leaderboard_source_id,
                'character_id' => $characters['all_dlc']->character_id,
            ]
        ]);
    
    
        /* ---------- Google Play ----------*/
        
        $leaderboard_source_id = $leaderboard_sources['google_play']->id;
        
        LeaderboardSourceCharacters::insert([
            [
                'leaderboard_source_id' => $leaderboard_source_id,
                'character_id' => $characters['cadence']->character_id,
            ],
            [
                'leaderboard_source_id' => $leaderboard_source_id,
                'character_id' => $characters['bard']->character_id,
            ],
            [
                'leaderboard_source_id' => $leaderboard_source_id,
                'character_id' => $characters['aria']->character_id,
            ],
            [
                'leaderboard_source_id' => $leaderboard_source_id,
                'character_id' => $characters['bolt']->character_id,
            ],
            [
                'leaderboard_source_id' => $leaderboard_source_id,
                'character_id' => $characters['monk']->character_id,
            ],
            [
                'leaderboard_source_id' => $leaderboard_source_id,
                'character_id' => $characters['dove']->character_id,
            ],
            [
                'leaderboard_source_id' => $leaderboard_source_id,
                'character_id' => $characters['eli']->character_id,
            ],
            [
                'leaderboard_source_id' => $leaderboard_source_id,
                'character_id' => $characters['melody']->character_id,
            ],
            [
                'leaderboard_source_id' => $leaderboard_source_id,
                'character_id' => $characters['dorian']->character_id,
            ],
            [
                'leaderboard_source_id' => $leaderboard_source_id,
                'character_id' => $characters['coda']->character_id,
            ],
            [
                'leaderboard_source_id' => $leaderboard_source_id,
                'character_id' => $characters['nocturna']->character_id,
            ],
            [
                'leaderboard_source_id' => $leaderboard_source_id,
                'character_id' => $characters['diamond']->character_id,
            ],
            [
                'leaderboard_source_id' => $leaderboard_source_id,
                'character_id' => $characters['mary']->character_id,
            ],
            [
                'leaderboard_source_id' => $leaderboard_source_id,
                'character_id' => $characters['tempo']->character_id,
            ],
            [
                'leaderboard_source_id' => $leaderboard_source_id,
                'character_id' => $characters['story']->character_id,
            ],
            [
                'leaderboard_source_id' => $leaderboard_source_id,
                'character_id' => $characters['all']->character_id,
            ],
            [
                'leaderboard_source_id' => $leaderboard_source_id,
                'character_id' => $characters['all_dlc']->character_id,
            ]
        ]);
    }
}
