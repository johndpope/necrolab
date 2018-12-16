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
        
        $mode_id = $modes['normal']->mode_id;
        
        ModeCharacters::insert([
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['cadence']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['bard']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['aria']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['bolt']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['monk']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['dove']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['eli']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['melody']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['dorian']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['coda']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['nocturna']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['diamond']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['mary']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['tempo']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['story']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['all']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['all_dlc']->character_id,
            ]
        ]);
        
    
        /* ---------- Hard ----------*/
        
        $mode_id = $modes['hard']->mode_id;
        
        ModeCharacters::insert([
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['cadence']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['bard']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['aria']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['bolt']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['monk']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['dove']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['eli']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['melody']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['dorian']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['coda']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['nocturna']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['diamond']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['mary']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['tempo']->character_id,
            ]
        ]);
        
        
        /* ---------- No Return ----------*/
        
        $mode_id = $modes['no_return']->mode_id;
        
        ModeCharacters::insert([
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['cadence']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['bard']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['aria']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['bolt']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['monk']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['dove']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['eli']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['melody']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['dorian']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['coda']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['nocturna']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['diamond']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['mary']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['tempo']->character_id,
            ]
        ]);
        
        
        /* ---------- Phasing ----------*/
        
        $mode_id = $modes['phasing']->mode_id;
        
        ModeCharacters::insert([
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['cadence']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['bard']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['aria']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['bolt']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['monk']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['dove']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['eli']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['melody']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['dorian']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['coda']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['nocturna']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['diamond']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['mary']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['tempo']->character_id,
            ]
        ]);
        
        
        /* ---------- Randomizer ----------*/
        
        $mode_id = $modes['randomizer']->mode_id;
        
        ModeCharacters::insert([
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['cadence']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['bard']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['aria']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['bolt']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['monk']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['dove']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['eli']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['melody']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['dorian']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['coda']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['nocturna']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['diamond']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['mary']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['tempo']->character_id,
            ]
        ]);
        
        
        /* ---------- Mystery ----------*/
        
        $mode_id = $modes['mystery']->mode_id;
        
        ModeCharacters::insert([
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['cadence']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['bard']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['aria']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['bolt']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['monk']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['dove']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['eli']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['melody']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['dorian']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['coda']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['nocturna']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['diamond']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['mary']->character_id,
            ],
            [
                'mode_id' => $mode_id,
                'character_id' => $characters['tempo']->character_id,
            ]
        ]);
    }
}
