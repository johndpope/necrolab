<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddDefaultField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {        
        /* ---------- Leaderboard Types ---------- */
    
        Schema::table('leaderboard_types', function (Blueprint $table) {
            $table->smallInteger('is_default')->nullable();
        });
        
        DB::update("
            UPDATE leaderboard_types
            SET is_default = 0
        ");
        
        DB::update("
            UPDATE leaderboard_types
            SET is_default = 1
            WHERE name = 'score'
        ");
        
        Schema::table('leaderboard_types', function (Blueprint $table) {
            $table->smallInteger('is_default')->nullable(false)->change();
        });
        
        
        /* ---------- Characters ---------- */
    
        Schema::table('characters', function (Blueprint $table) {
            $table->smallInteger('is_default')->nullable();
        });
        
        DB::update("
            UPDATE characters
            SET is_default = 0
        ");
        
        DB::update("
            UPDATE characters
            SET is_default = 1
            WHERE name = 'cadence'
        ");
        
        Schema::table('characters', function (Blueprint $table) {
            $table->smallInteger('is_default')->nullable(false)->change();
        });
        
        
        /* ---------- Releases ---------- */
    
        Schema::table('releases', function (Blueprint $table) {
            $table->smallInteger('is_default')->nullable();
        });
        
        DB::update("
            UPDATE releases
            SET is_default = 0
        ");
        
        DB::update("
            UPDATE releases
            SET is_default = 1
            WHERE name = 'amplified_dlc'
        ");
        
        Schema::table('releases', function (Blueprint $table) {
            $table->smallInteger('is_default')->nullable(false)->change();
        });
        
        
        /* ---------- Modes ---------- */
    
        Schema::table('modes', function (Blueprint $table) {
            $table->smallInteger('is_default')->nullable();
        });
        
        DB::update("
            UPDATE modes
            SET is_default = 0
        ");
        
        DB::update("
            UPDATE modes
            SET is_default = 1
            WHERE name = 'normal'
        ");
        
        Schema::table('modes', function (Blueprint $table) {
            $table->smallInteger('is_default')->nullable(false)->change();
        });
        
        
        /* ---------- Seeded Types ---------- */
    
        Schema::table('seeded_types', function (Blueprint $table) {
            $table->smallInteger('is_default')->nullable();
        });
        
        DB::update("
            UPDATE seeded_types
            SET is_default = 0
        ");
        
        DB::update("
            UPDATE seeded_types
            SET is_default = 1
            WHERE name = 'unseeded'
        ");
        
        Schema::table('seeded_types', function (Blueprint $table) {
            $table->smallInteger('is_default')->nullable(false)->change();
        });
        
        
        /* ---------- Multiplayer Types ---------- */
    
        Schema::table('multiplayer_types', function (Blueprint $table) {
            $table->smallInteger('is_default')->nullable();
        });
        
        DB::update("
            UPDATE multiplayer_types
            SET is_default = 0
        ");
        
        DB::update("
            UPDATE multiplayer_types
            SET is_default = 1
            WHERE name = 'single'
        ");
        
        Schema::table('multiplayer_types', function (Blueprint $table) {
            $table->smallInteger('is_default')->nullable(false)->change();
        });
        
        
        /* ---------- Soundtracks ---------- */
    
        Schema::table('soundtracks', function (Blueprint $table) {
            $table->smallInteger('is_default')->nullable();
        });
        
        DB::update("
            UPDATE soundtracks
            SET is_default = 0
        ");
        
        DB::update("
            UPDATE soundtracks
            SET is_default = 1
            WHERE name = 'default'
        ");
        
        Schema::table('soundtracks', function (Blueprint $table) {
            $table->smallInteger('is_default')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('leaderboard_types', function (Blueprint $table) {            
            $table->dropColumn('is_default');
        }); 
        
        Schema::table('characters', function (Blueprint $table) {            
            $table->dropColumn('is_default');
        });  
        
        Schema::table('releases', function (Blueprint $table) {            
            $table->dropColumn('is_default');
        });    
        
        Schema::table('modes', function (Blueprint $table) {            
            $table->dropColumn('is_default');
        });    
        
        Schema::table('seeded_types', function (Blueprint $table) {            
            $table->dropColumn('is_default');
        });    
        
        Schema::table('multiplayer_types', function (Blueprint $table) {            
            $table->dropColumn('is_default');
        });    
        
        Schema::table('soundtracks', function (Blueprint $table) {            
            $table->dropColumn('is_default');
        });    
    }
}
