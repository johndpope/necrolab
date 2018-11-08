<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddLeaderboardTypesDetailsName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {                
        Schema::table('leaderboard_types', function (Blueprint $table) {            
            $table->string('details_field_name')->nullable();
        });
        
        DB::update("
            UPDATE leaderboard_types
            SET details_field_name = 'score'
            WHERE name IN ('score', 'daily');
        ");
        
        DB::update("
            UPDATE leaderboard_types
            SET details_field_name = 'time'
            WHERE name = 'speed';
        ");
        
        DB::update("
            UPDATE leaderboard_types
            SET details_field_name = 'win_count'
            WHERE name = 'deathless';
        ");
        
        Schema::table('leaderboard_types', function (Blueprint $table) {            
            $table->string('details_field_name')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('leaderboard_types', function (Blueprint $table) {            
            $table->dropColumn('details_field_name');
        });
    }
}
