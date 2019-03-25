<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Soundtracks;

class SoundtracksSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        Soundtracks::insert([
            [
                'name' => 'default',
                'display_name' => 'Default',
                'is_default' => 1,
                'sort_order' => 1
            ],
            [
                'name' => 'custom',
                'display_name' => 'Custom',
                'is_default' => 0,
                'sort_order' => 2
            ]
        ]);
    }
}
