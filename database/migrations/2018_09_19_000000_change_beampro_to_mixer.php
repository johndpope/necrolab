<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class ChangeBeamproToMixer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {                
        DB::update("
            UPDATE external_sites
            SET 
                name = 'mixer',
                display_name = 'Mixer'
            WHERE name = 'beampro'
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {

    }
}
