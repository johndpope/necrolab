<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\ModeCharacters;
use App\Characters;
use App\Modes;

class ModeCharactersSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $modes = Modes::getAllByName();
        $characters = Characters::getAllByName();
    
    
        /* ---------- Normal ----------*/
        
        $mode_id = $modes['normal']->id;
        
        ModeCharacters::insert([
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['cadence']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['bard']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['aria']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['bolt']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['monk']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['dove']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['eli']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['melody']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['dorian']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['coda']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['nocturna']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['diamond']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['mary']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['tempo']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['story']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['all']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['all_dlc']->id,
            ]
        ]);
        
    
        /* ---------- Hard ----------*/
        
        $mode_id = $modes['hard']->id;
        
        ModeCharacters::insert([
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['cadence']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['bard']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['aria']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['bolt']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['monk']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['dove']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['eli']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['melody']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['dorian']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['coda']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['nocturna']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['diamond']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['mary']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['tempo']->id,
            ]
        ]);
        
        
        /* ---------- No Return ----------*/
        
        $mode_id = $modes['no_return']->id;
        
        ModeCharacters::insert([
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['cadence']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['bard']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['aria']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['bolt']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['monk']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['dove']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['eli']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['melody']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['dorian']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['coda']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['nocturna']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['diamond']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['mary']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['tempo']->id,
            ]
        ]);
        
        
        /* ---------- Phasing ----------*/
        
        $mode_id = $modes['phasing']->id;
        
        ModeCharacters::insert([
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['cadence']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['bard']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['aria']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['bolt']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['monk']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['dove']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['eli']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['melody']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['dorian']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['coda']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['nocturna']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['diamond']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['mary']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['tempo']->id,
            ]
        ]);
        
        
        /* ---------- Randomizer ----------*/
        
        $mode_id = $modes['randomizer']->id;
        
        ModeCharacters::insert([
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['cadence']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['bard']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['aria']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['bolt']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['monk']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['dove']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['eli']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['melody']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['dorian']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['coda']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['nocturna']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['diamond']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['mary']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['tempo']->id,
            ]
        ]);
        
        
        /* ---------- Mystery ----------*/
        
        $mode_id = $modes['mystery']->id;
        
        ModeCharacters::insert([
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['cadence']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['bard']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['aria']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['bolt']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['monk']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['dove']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['eli']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['melody']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['dorian']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['coda']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['nocturna']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['diamond']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['mary']->id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['tempo']->id,
            ]
        ]);
    }
}
