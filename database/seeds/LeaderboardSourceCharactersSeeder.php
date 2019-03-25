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
                'character_id' => $characters['cadence']->id,
            ],
            [
                'leaderboard_source_id' => $leaderboard_source_id,
                'character_id' => $characters['bard']->id,
            ],
            [
                'leaderboard_source_id' => $leaderboard_source_id,
                'character_id' => $characters['aria']->id,
            ],
            [
                'leaderboard_source_id' => $leaderboard_source_id,
                'character_id' => $characters['bolt']->id,
            ],
            [
                'leaderboard_source_id' => $leaderboard_source_id,
                'character_id' => $characters['monk']->id,
            ],
            [
                'leaderboard_source_id' => $leaderboard_source_id,
                'character_id' => $characters['dove']->id,
            ],
            [
                'leaderboard_source_id' => $leaderboard_source_id,
                'character_id' => $characters['eli']->id,
            ],
            [
                'leaderboard_source_id' => $leaderboard_source_id,
                'character_id' => $characters['melody']->id,
            ],
            [
                'leaderboard_source_id' => $leaderboard_source_id,
                'character_id' => $characters['dorian']->id,
            ],
            [
                'leaderboard_source_id' => $leaderboard_source_id,
                'character_id' => $characters['coda']->id,
            ],
            [
                'leaderboard_source_id' => $leaderboard_source_id,
                'character_id' => $characters['nocturna']->id,
            ],
            [
                'leaderboard_source_id' => $leaderboard_source_id,
                'character_id' => $characters['diamond']->id,
            ],
            [
                'leaderboard_source_id' => $leaderboard_source_id,
                'character_id' => $characters['mary']->id,
            ],
            [
                'leaderboard_source_id' => $leaderboard_source_id,
                'character_id' => $characters['tempo']->id,
            ],
            [
                'leaderboard_source_id' => $leaderboard_source_id,
                'character_id' => $characters['story']->id,
            ],
            [
                'leaderboard_source_id' => $leaderboard_source_id,
                'character_id' => $characters['all']->id,
            ],
            [
                'leaderboard_source_id' => $leaderboard_source_id,
                'character_id' => $characters['all_dlc']->id,
            ]
        ]);
    }
}
