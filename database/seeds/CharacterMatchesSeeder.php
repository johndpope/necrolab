<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\LeaderboardSources;
use App\CharacterMatches;
use App\Characters;

class CharacterMatchesSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $leaderboard_source = LeaderboardSources::getByName('steam');
        $characters = Characters::getAllByName();
        
        CharacterMatches::insert([
            [
                'leaderboard_source_id' => $leaderboard_source->id,
                'character_id' => $characters['bard']->id,
                'match_regex' => '(bard)',
                'sort_order' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'leaderboard_source_id' => $leaderboard_source->id,
                'character_id' => $characters['aria']->id,
                'match_regex' => '(aria)',
                'sort_order' => 2,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'leaderboard_source_id' => $leaderboard_source->id,
                'character_id' => $characters['bolt']->id,
                'match_regex' => '(bolt)',
                'sort_order' => 3,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'leaderboard_source_id' => $leaderboard_source->id,
                'character_id' => $characters['monk']->id,
                'match_regex' => '(monk)',
                'sort_order' => 4,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'leaderboard_source_id' => $leaderboard_source->id,
                'character_id' => $characters['dove']->id,
                'match_regex' => '(dove)',
                'sort_order' => 5,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'leaderboard_source_id' => $leaderboard_source->id,
                'character_id' => $characters['eli']->id,
                'match_regex' => '(eli)',
                'sort_order' => 6,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'leaderboard_source_id' => $leaderboard_source->id,
                'character_id' => $characters['melody']->id,
                'match_regex' => '(melody)',
                'sort_order' => 7,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'leaderboard_source_id' => $leaderboard_source->id,
                'character_id' => $characters['dorian']->id,
                'match_regex' => '(dorian)',
                'sort_order' => 8,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'leaderboard_source_id' => $leaderboard_source->id,
                'character_id' => $characters['coda']->id,
                'match_regex' => '(coda)',
                'sort_order' => 9,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'leaderboard_source_id' => $leaderboard_source->id,
                'character_id' => $characters['nocturna']->id,
                'match_regex' => '(nocturna)',
                'sort_order' => 10,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'leaderboard_source_id' => $leaderboard_source->id,
                'character_id' => $characters['diamond']->id,
                'match_regex' => '(diamond)',
                'sort_order' => 11,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'leaderboard_source_id' => $leaderboard_source->id,
                'character_id' => $characters['mary']->id,
                'match_regex' => '(mary)',
                'sort_order' => 12,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'leaderboard_source_id' => $leaderboard_source->id,
                'character_id' => $characters['tempo']->id,
                'match_regex' => '(tempo)',
                'sort_order' => 13,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'leaderboard_source_id' => $leaderboard_source->id,
                'character_id' => $characters['story']->id,
                'match_regex' => '(story)',
                'sort_order' => 14,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'leaderboard_source_id' => $leaderboard_source->id,
                'character_id' => $characters['all_dlc']->id,
                'match_regex' => '(all\ chars\ dlc)',
                'sort_order' => 15,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'leaderboard_source_id' => $leaderboard_source->id,
                'character_id' => $characters['all']->id,
                'match_regex' => '(all\ chars)',
                'sort_order' => 16,
                'created_at' => date('Y-m-d H:i:s')
            ]
        ]);
    }
}
