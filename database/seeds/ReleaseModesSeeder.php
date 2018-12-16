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
        
        $release_id = $releases['alpha']->release_id;
        
        ReleaseModes::insert([
            [
                'release_id' => $release_id,
                'mode_id' => $modes['normal']->mode_id,
            ]
        ]);
        
        
        /* ---------- Early Access ----------*/
        
        $release_id = $releases['early_access']->release_id;
        
        ReleaseModes::insert([
            [
                'release_id' => $release_id,
                'mode_id' => $modes['normal']->mode_id,
            ]
        ]);
    
    
        /* ---------- Original ----------*/
        
        $release_id = $releases['original']->release_id;
        
        ReleaseModes::insert([
            [
                'release_id' => $release_id,
                'mode_id' => $modes['normal']->mode_id,
            ]
        ]);
        
        
        /* ---------- Amplified Early Access ----------*/
        
        $release_id = $releases['amplified_dlc_early_access']->release_id;
        
        ReleaseModes::insert([
            [
                'release_id' => $release_id,
                'mode_id' => $modes['normal']->mode_id,
            ],
            [
                'release_id' => $release_id,
                'mode_id' => $modes['hard']->mode_id,
            ],
            [
                'release_id' => $release_id,
                'mode_id' => $modes['no_return']->mode_id,
            ],
            [
                'release_id' => $release_id,
                'mode_id' => $modes['phasing']->mode_id,
            ],
            [
                'release_id' => $release_id,
                'mode_id' => $modes['randomizer']->mode_id,
            ],
            [
                'release_id' => $release_id,
                'mode_id' => $modes['mystery']->mode_id,
            ]
        ]);
        
        
        /* ---------- Amplified----------*/
        
        $release_id = $releases['amplified_dlc']->release_id;
        
        ReleaseModes::insert([
            [
                'release_id' => $release_id,
                'mode_id' => $modes['normal']->mode_id,
            ],
            [
                'release_id' => $release_id,
                'mode_id' => $modes['hard']->mode_id,
            ],
            [
                'release_id' => $release_id,
                'mode_id' => $modes['no_return']->mode_id,
            ],
            [
                'release_id' => $release_id,
                'mode_id' => $modes['phasing']->mode_id,
            ],
            [
                'release_id' => $release_id,
                'mode_id' => $modes['randomizer']->mode_id,
            ],
            [
                'release_id' => $release_id,
                'mode_id' => $modes['mystery']->mode_id,
            ]
        ]);
    }
}
