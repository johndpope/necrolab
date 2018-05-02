<?php

use Illuminate\Database\Seeder;
use App\Characters;

class SetCharactersSteamMatch extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $cadence = Characters::where('name', 'cadence')->first();
        
        $cadence->steam_match = 'cadence';
        
        $cadence->save();
        
    
        $bard = Characters::where('name', 'bard')->first();
        
        $bard->steam_match = 'bard';
        
        $bard->save();
        
        
        $aria = Characters::where('name', 'aria')->first();
        
        $aria->steam_match = 'aria';
        
        $aria->save();
        
        
        $bolt = Characters::where('name', 'bolt')->first();
        
        $bolt->steam_match = 'bolt';
        
        $bolt->save();
        
        
        $monk = Characters::where('name', 'monk')->first();
        
        $monk->steam_match = 'monk';
        
        $monk->save();
        
        
        $dove = Characters::where('name', 'dove')->first();
        
        $dove->steam_match = 'dove';
        
        $dove->save();
        
        
        $eli = Characters::where('name', 'eli')->first();
        
        $eli->steam_match = 'eli';
        
        $eli->save();
        
        
        $melody = Characters::where('name', 'melody')->first();
        
        $melody->steam_match = 'melody';
        
        $melody->save();
        
        
        $dorian = Characters::where('name', 'dorian')->first();
        
        $dorian->steam_match = 'dorian';
        
        $dorian->save();
        
        
        $coda = Characters::where('name', 'coda')->first();
        
        $coda->steam_match = 'coda';
        
        $coda->save();
        
        
        $ghost = Characters::where('name', 'ghost')->first();
        
        $ghost->steam_match = 'ghost';
        
        $ghost->save();
        
        
        $pacifist = Characters::where('name', 'pacifist')->first();
        
        $pacifist->steam_match = 'pacifist';
        
        $pacifist->save();
        
        
        $thief = Characters::where('name', 'thief')->first();
        
        $thief->steam_match = 'thief';
        
        $thief->save();
        
        
        $all = Characters::where('name', 'all')->first();
        
        $all->steam_match = 'all chars';
        
        $all->save();
        
        
        $story = Characters::where('name', 'story')->first();
        
        $story->steam_match = 'story';
        
        $story->save();
        
        
        $nocturna = Characters::where('name', 'nocturna')->first();
        
        $nocturna->steam_match = 'nocturna';
        
        $nocturna->save();
        
        
        $diamond = Characters::where('name', 'diamond')->first();
        
        $diamond->steam_match = 'diamond';
        
        $diamond->save();
        
        
        $mary = Characters::where('name', 'mary')->first();
        
        $mary->steam_match = 'mary';
        
        $mary->save();
        
        
        $tempo = Characters::where('name', 'tempo')->first();
        
        $tempo->steam_match = 'tempo';
        
        $tempo->save();
        
        
        $all_dlc = Characters::where('name', 'all_dlc')->first();
        
        $all_dlc->steam_match = 'all chars dlc';
        
        $all_dlc->save();
    }
}
