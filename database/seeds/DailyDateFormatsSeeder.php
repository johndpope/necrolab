<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\LeaderboardSources;
use App\DailyDateFormats;

class DailyDateFormatsSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $leaderboard_source = LeaderboardSources::getByName('steam');
        
        DailyDateFormats::insert([
            [
                'leaderboard_source_id' => $leaderboard_source->id,
                'clean_regex' => '[^0-9\/]',
                'format' => 'd/m/Y',
                'created_at' => date('Y-m-d H:i:s')
            ]
        ]);
    }
}
