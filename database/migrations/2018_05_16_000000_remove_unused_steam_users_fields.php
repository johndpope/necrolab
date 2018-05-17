<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveUnusedSteamUsersFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {    
        Schema::table('steam_users', function (Blueprint $table) {
            $table->dropColumn('lastlogoff');
            $table->dropColumn('personastate');
            $table->dropColumn('realname');
            $table->dropColumn('primaryclanid');
            $table->dropColumn('timecreated');
            $table->dropColumn('personastateflags');
            $table->dropColumn('loccountrycode');
            $table->dropColumn('locstatecode');
            $table->dropColumn('loccityid');
            $table->dropColumn('twitch_username');
            $table->dropColumn('twitter_username');
            $table->dropColumn('nico_nico_url');
            $table->dropColumn('hitbox_username');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
    }
}