<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddLeaderboardTypeFlags extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {        
        Schema::table('leaderboard_types', function (Blueprint $table) {
            $table->smallInteger('show_seed')->nullable(false)->default(0);
            $table->smallInteger('show_replay')->nullable(false)->default(0);
            $table->smallInteger('show_zone_level')->nullable(false)->default(0);
        });
        
        DB::update("
            UPDATE leaderboard_types
            SET show_seed = 1
            WHERE name IN ('score', 'speed', 'daily')
        ");
        
        DB::update("
            UPDATE leaderboard_types
            SET show_replay = 1
            WHERE name IN ('score', 'speed')
        ");
        
        DB::update("
            UPDATE leaderboard_types
            SET show_zone_level = 1
            WHERE name IN ('score', 'deathless', 'daily')
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('leaderboard_types', function (Blueprint $table) {            
            $table->dropColumn('show_seed');
            $table->dropColumn('show_replay');
            $table->dropColumn('show_zone_level');
        });    
    }
}
