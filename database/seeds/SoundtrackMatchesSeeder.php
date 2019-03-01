<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\LeaderboardSources;
use App\SoundtrackMatches;
use App\Soundtracks;

class SoundtrackMatchesSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $leaderboard_source = LeaderboardSources::getByName('steam');
        $soundtracks = Soundtracks::getAllByName();
        
        SoundtrackMatches::insert([
            [
                'leaderboard_source_id' => $leaderboard_source->id,
                'soundtrack_id' => $soundtracks['custom']->id,
                'match_regex' => '.*(custom).*',
                'sort_order' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ]
        ]);
    }
}
