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
                'display_name' => 'Default'
            ],
            [
                'name' => 'custom',
                'display_name' => 'Custom'
            ]
        ]);
    }
}
