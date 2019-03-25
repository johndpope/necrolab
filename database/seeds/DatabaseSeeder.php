<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            RankingTypesSeeder::class,
            DataTypesSeeder::class,
            LeaderboardDetailsColumnsSeeder::class,
            DailyRankingDayTypesSeeder::class,
            ExternalSitesSeeder::class,
            LeaderboardSourcesSeeder::class,
            LeaderboardTypesSeeder::class,
            CharactersSeeder::class,
            ReleasesSeeder::class,
            ModesSeeder::class,
            SeededTypesSeeder::class,
            MultiplayerTypesSeeder::class,
            SoundtracksSeeder::class,
            LeaderboardTypeMatchesSeeder::class,
            CharacterMatchesSeeder::class,
            ReleaseMatchesSeeder::class,
            ModeMatchesSeeder::class,
            SeededTypeMatchesSeeder::class,
            MultiplayerTypeMatchesSeeder::class,
            SoundtrackMatchesSeeder::class,
            DailyDateFormatsSeeder::class,
            LeaderboardSourceCharactersSeeder::class,
            LeaderboardSourceReleasesSeeder::class,
            LeaderboardSourceMultiplayerTypesSeeder::class,
            LeaderboardTypeCharactersSeeder::class,
            LeaderboardTypeModesSeeder::class,
            LeaderboardTypeDetailsColumnsSeeder::class,
            ReleaseCharactersSeeder::class,
            ReleaseModesSeeder::class,
            ModeCharactersSeeder::class
        ]);
    }
}
