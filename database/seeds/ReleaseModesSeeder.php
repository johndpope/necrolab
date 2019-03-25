<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\ReleaseModes;
use App\Releases;
use App\Modes;

class ReleaseModesSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $releases = Releases::getAllByName();
        $modes = Modes::getAllByName();
        
        
        /* ---------- Alpha ----------*/
        
        $release_id = $releases['alpha']->id;
        
        ReleaseModes::insert([
            [
                'release_id' => $release_id,
                'mode_id' => $modes['normal']->id,
            ]
        ]);
        
        
        /* ---------- Early Access ----------*/
        
        $release_id = $releases['early_access']->id;
        
        ReleaseModes::insert([
            [
                'release_id' => $release_id,
                'mode_id' => $modes['normal']->id,
            ]
        ]);
    
    
        /* ---------- Original ----------*/
        
        $release_id = $releases['original']->id;
        
        ReleaseModes::insert([
            [
                'release_id' => $release_id,
                'mode_id' => $modes['normal']->id,
            ]
        ]);
        
        
        /* ---------- Amplified Early Access ----------*/
        
        $release_id = $releases['amplified_dlc_early_access']->id;
        
        ReleaseModes::insert([
            [
                'release_id' => $release_id,
                'mode_id' => $modes['normal']->id,
            ],
            [
                'release_id' => $release_id,
                'mode_id' => $modes['hard']->id,
            ],
            [
                'release_id' => $release_id,
                'mode_id' => $modes['no_return']->id,
            ],
            [
                'release_id' => $release_id,
                'mode_id' => $modes['phasing']->id,
            ],
            [
                'release_id' => $release_id,
                'mode_id' => $modes['randomizer']->id,
            ],
            [
                'release_id' => $release_id,
                'mode_id' => $modes['mystery']->id,
            ]
        ]);
        
        
        /* ---------- Amplified----------*/
        
        $release_id = $releases['amplified_dlc']->id;
        
        ReleaseModes::insert([
            [
                'release_id' => $release_id,
                'mode_id' => $modes['normal']->id,
            ],
            [
                'release_id' => $release_id,
                'mode_id' => $modes['hard']->id,
            ],
            [
                'release_id' => $release_id,
                'mode_id' => $modes['no_return']->id,
            ],
            [
                'release_id' => $release_id,
                'mode_id' => $modes['phasing']->id,
            ],
            [
                'release_id' => $release_id,
                'mode_id' => $modes['randomizer']->id,
            ],
            [
                'release_id' => $release_id,
                'mode_id' => $modes['mystery']->id,
            ]
        ]);
    }
}
