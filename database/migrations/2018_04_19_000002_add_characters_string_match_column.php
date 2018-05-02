<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCharactersStringMatchColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('characters', function (Blueprint $table) {
            $table->string('steam_match')->nullable();
        });
        
        Artisan::call('db:seed', [
            '--class' => 'SetCharactersSteamMatch',
            '--force' => true 
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('characters', function (Blueprint $table) {
            $table->dropColumn('steam_match');
        });
    }
}