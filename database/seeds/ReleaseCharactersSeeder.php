<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\ReleaseCharacters;
use App\Characters;
use App\Releases;

class ReleaseCharactersSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $releases = Releases::getAllByName();
        $characters = Characters::getAllByName();
        
        
        /* ---------- Alpha ----------*/
        
        $release_id = $releases['alpha']->id;
        
        ReleaseCharacters::insert([
            [
                'release_id' => $release_id,
                'character_id' => $characters['cadence']->id,
            ]
        ]);
        
        
        /* ---------- Early Access ----------*/
        
        $release_id = $releases['early_access']->id;
        
        ReleaseCharacters::insert([
            [
                'release_id' => $release_id,
                'character_id' => $characters['cadence']->id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['bard']->id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['aria']->id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['bolt']->id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['monk']->id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['dove']->id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['eli']->id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['melody']->id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['dorian']->id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['story']->id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['all']->id,
            ]
        ]);
    
    
        /* ---------- Original ----------*/
        
        $release_id = $releases['original']->id;
        
        ReleaseCharacters::insert([
            [
                'release_id' => $release_id,
                'character_id' => $characters['cadence']->id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['bard']->id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['aria']->id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['bolt']->id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['monk']->id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['dove']->id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['eli']->id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['melody']->id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['dorian']->id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['coda']->id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['story']->id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['all']->id,
            ]
        ]);
        
        
        /* ---------- Amplified Early Access ----------*/
        
        $release_id = $releases['amplified_dlc_early_access']->id;
        
        ReleaseCharacters::insert([
            [
                'release_id' => $release_id,
                'character_id' => $characters['cadence']->id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['bard']->id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['aria']->id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['bolt']->id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['monk']->id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['dove']->id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['eli']->id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['melody']->id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['dorian']->id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['coda']->id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['nocturna']->id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['diamond']->id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['mary']->id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['tempo']->id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['story']->id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['all']->id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['all_dlc']->id,
            ]
        ]);
        
    
        /* ---------- Amplified ----------*/
        
        $release_id = $releases['amplified_dlc']->id;
        
        ReleaseCharacters::insert([
            [
                'release_id' => $release_id,
                'character_id' => $characters['cadence']->id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['bard']->id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['aria']->id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['bolt']->id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['monk']->id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['dove']->id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['eli']->id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['melody']->id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['dorian']->id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['coda']->id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['nocturna']->id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['diamond']->id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['mary']->id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['tempo']->id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['story']->id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['all']->id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['all_dlc']->id,
            ]
        ]);
    }
}
