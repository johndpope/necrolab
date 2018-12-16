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
        
        $release_id = $releases['alpha']->release_id;
        
        ReleaseCharacters::insert([
            [
                'release_id' => $release_id,
                'character_id' => $characters['cadence']->character_id,
            ]
        ]);
        
        
        /* ---------- Early Access ----------*/
        
        $release_id = $releases['early_access']->release_id;
        
        ReleaseCharacters::insert([
            [
                'release_id' => $release_id,
                'character_id' => $characters['cadence']->character_id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['bard']->character_id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['aria']->character_id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['bolt']->character_id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['monk']->character_id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['dove']->character_id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['eli']->character_id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['melody']->character_id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['dorian']->character_id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['story']->character_id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['all']->character_id,
            ]
        ]);
    
    
        /* ---------- Original ----------*/
        
        $release_id = $releases['original']->release_id;
        
        ReleaseCharacters::insert([
            [
                'release_id' => $release_id,
                'character_id' => $characters['cadence']->character_id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['bard']->character_id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['aria']->character_id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['bolt']->character_id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['monk']->character_id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['dove']->character_id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['eli']->character_id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['melody']->character_id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['dorian']->character_id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['coda']->character_id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['story']->character_id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['all']->character_id,
            ]
        ]);
        
        
        /* ---------- Amplified Early Access ----------*/
        
        $release_id = $releases['amplified_dlc_early_access']->release_id;
        
        ReleaseCharacters::insert([
            [
                'release_id' => $release_id,
                'character_id' => $characters['cadence']->character_id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['bard']->character_id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['aria']->character_id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['bolt']->character_id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['monk']->character_id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['dove']->character_id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['eli']->character_id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['melody']->character_id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['dorian']->character_id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['coda']->character_id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['nocturna']->character_id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['diamond']->character_id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['mary']->character_id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['tempo']->character_id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['story']->character_id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['all']->character_id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['all_dlc']->character_id,
            ]
        ]);
        
    
        /* ---------- Amplified ----------*/
        
        $release_id = $releases['amplified_dlc']->release_id;
        
        ReleaseCharacters::insert([
            [
                'release_id' => $release_id,
                'character_id' => $characters['cadence']->character_id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['bard']->character_id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['aria']->character_id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['bolt']->character_id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['monk']->character_id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['dove']->character_id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['eli']->character_id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['melody']->character_id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['dorian']->character_id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['coda']->character_id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['nocturna']->character_id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['diamond']->character_id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['mary']->character_id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['tempo']->character_id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['story']->character_id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['all']->character_id,
            ],
            [
                'release_id' => $release_id,
                'character_id' => $characters['all_dlc']->character_id,
            ]
        ]);
    }
}
