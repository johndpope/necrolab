<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Characters;

class CharactersSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        Characters::insert([
            [
                'name' => 'cadence',
                'display_name' => 'Cadence',
                'is_active' => 1,
                'sort_order' => 1,
                'is_default' => 1
            ],
            [
                'name' => 'bard',
                'display_name' => 'Bard',
                'is_active' => 1,
                'sort_order' => 2,
                'is_default' => 0
            ],
            [
                'name' => 'aria',
                'display_name' => 'Aria',
                'is_active' => 1,
                'sort_order' => 3,
                'is_default' => 0
            ],
            [
                'name' => 'bolt',
                'display_name' => 'Bolt',
                'is_active' => 1,
                'sort_order' => 4,
                'is_default' => 0
            ],
            [
                'name' => 'monk',
                'display_name' => 'Monk',
                'is_active' => 1,
                'sort_order' => 5,
                'is_default' => 0
            ],
            [
                'name' => 'dove',
                'display_name' => 'Dove',
                'is_active' => 1,
                'sort_order' => 6,
                'is_default' => 0
            ],
            [
                'name' => 'eli',
                'display_name' => 'Eli',
                'is_active' => 1,
                'sort_order' => 7,
                'is_default' => 0
            ],
            [
                'name' => 'melody',
                'display_name' => 'Melody',
                'is_active' => 1,
                'sort_order' => 8,
                'is_default' => 0
            ],
            [
                'name' => 'dorian',
                'display_name' => 'Dorian',
                'is_active' => 1,
                'sort_order' => 9,
                'is_default' => 0
            ],
            [
                'name' => 'coda',
                'display_name' => 'Coda',
                'is_active' => 1,
                'sort_order' => 10,
                'is_default' => 0
            ],
            [
                'name' => 'nocturna',
                'display_name' => 'Nocturna',
                'is_active' => 1,
                'sort_order' => 11,
                'is_default' => 0
            ],
            [
                'name' => 'diamond',
                'display_name' => 'Diamond',
                'is_active' => 1,
                'sort_order' => 12,
                'is_default' => 0
            ],
            [
                'name' => 'mary',
                'display_name' => 'Mary',
                'is_active' => 1,
                'sort_order' => 13,
                'is_default' => 0
            ],
            [
                'name' => 'tempo',
                'display_name' => 'Tempo',
                'is_active' => 1,
                'sort_order' => 14,
                'is_default' => 0
            ],
            [
                'name' => 'story',
                'display_name' => 'Story',
                'is_active' => 1,
                'sort_order' => 15,
                'is_default' => 0
            ],
            [
                'name' => 'all',
                'display_name' => 'All Chars',
                'is_active' => 1,
                'sort_order' => 16,
                'is_default' => 0
            ],
            [
                'name' => 'all_dlc',
                'display_name' => 'All Chars DLC',
                'is_active' => 1,
                'sort_order' => 17,
                'is_default' => 0
            ],
        ]);
    }
}
