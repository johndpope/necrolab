<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\ExternalSites;

class ExternalSitesSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        ExternalSites::insert([
            [
                'name' => 'discord',
                'display_name' => 'Discord',
                'enabled' => 1,
                'sort_order' => 1
            ],
            [
                'name' => 'mixer',
                'display_name' => 'Mixer',
                'enabled' => 1,
                'sort_order' => 2
            ],
            [
                'name' => 'reddit',
                'display_name' => 'Reddit',
                'enabled' => 1,
                'sort_order' => 3
            ],
            [
                'name' => 'twitch',
                'display_name' => 'Twitch',
                'enabled' => 1,
                'sort_order' => 4
            ],
            [
                'name' => 'twitter',
                'display_name' => 'Twitter',
                'enabled' => 1,
                'sort_order' => 5
            ],
            [
                'name' => 'youtube',
                'display_name' => 'Youtube',
                'enabled' => 1,
                'sort_order' => 6
            ]
        ]);
    }
}
