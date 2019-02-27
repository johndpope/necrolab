<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\LeaderboardSources;
use App\ModeMatches;
use App\Modes;

class ModeMatchesSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $leaderboard_source = LeaderboardSources::getByName('steam');
        $modes = Modes::getAllByName();
        
        ModeMatches::insert([
            [
                'leaderboard_source_id' => $leaderboard_source->id,
                'mode_id' => $modes['hard']->id,
                'match_regex' => '(hard){2,}|(?!.*hardcore).*hard.*',
                'sort_order' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'leaderboard_source_id' => $leaderboard_source->id,
                'mode_id' => $modes['no_return']->id,
                'match_regex' => '(no\ return)',
                'sort_order' => 2,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'leaderboard_source_id' => $leaderboard_source->id,
                'mode_id' => $modes['phasing']->id,
                'match_regex' => '(phasing)',
                'sort_order' => 3,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'leaderboard_source_id' => $leaderboard_source->id,
                'mode_id' => $modes['randomizer']->id,
                'match_regex' => '(randomizer)',
                'sort_order' => 4,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'leaderboard_source_id' => $leaderboard_source->id,
                'mode_id' => $modes['mystery']->id,
                'match_regex' => '(mystery)',
                'sort_order' => 5,
                'created_at' => date('Y-m-d H:i:s')
            ],
        ]);
    }
}
