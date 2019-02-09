<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddMatchCriteriaTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /* ---------- Leaderboard Types ---------- */
    
        Schema::create('leaderboard_type_matches', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->smallInteger('leaderboard_source_id');
            $table->smallInteger('leaderboard_type_id');
            $table->text('match_regex');
            $table->smallInteger('sort_order');
            $table->timestamps();
            
            $table->unique([
                'leaderboard_source_id',
                'leaderboard_type_id'
            ]);
            
            $table->foreign('leaderboard_source_id')
                ->references('id')
                ->on('leaderboard_sources')
                ->onDelete('cascade')
                ->onUpdate('cascade');
                
            $table->foreign('leaderboard_type_id')
                ->references('id')
                ->on('leaderboard_types')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
        
        Artisan::call('db:seed', [
            '--class' => 'LeaderboardTypeMatchesSeeder',
            '--force' => true 
        ]);
        
        
        /* ---------- Characters ---------- */
    
        Schema::create('character_matches', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->smallInteger('leaderboard_source_id');
            $table->smallInteger('character_id');
            $table->text('match_regex');
            $table->smallInteger('sort_order');
            $table->timestamps();
            
            $table->unique([
                'leaderboard_source_id',
                'character_id'
            ]);
            
            $table->foreign('leaderboard_source_id')
                ->references('id')
                ->on('leaderboard_sources')
                ->onDelete('cascade')
                ->onUpdate('cascade');
                
            $table->foreign('character_id')
                ->references('id')
                ->on('characters')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
        
        Artisan::call('db:seed', [
            '--class' => 'CharacterMatchesSeeder',
            '--force' => true 
        ]);
        
        
        /* ---------- Releases ---------- */
    
        Schema::create('release_matches', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->smallInteger('leaderboard_source_id');
            $table->smallInteger('release_id');
            $table->text('match_regex');
            $table->smallInteger('sort_order');
            $table->timestamps();
            
            $table->unique([
                'leaderboard_source_id',
                'release_id'
            ]);
            
            $table->foreign('leaderboard_source_id')
                ->references('id')
                ->on('leaderboard_sources')
                ->onDelete('cascade')
                ->onUpdate('cascade');
                
            $table->foreign('release_id')
                ->references('id')
                ->on('releases')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
        
        Artisan::call('db:seed', [
            '--class' => 'ReleaseMatchesSeeder',
            '--force' => true 
        ]);
        
        
        /* ---------- Modes ---------- */
    
        Schema::create('mode_matches', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->smallInteger('leaderboard_source_id');
            $table->smallInteger('mode_id');
            $table->text('match_regex');
            $table->smallInteger('sort_order');
            $table->timestamps();
            
            $table->unique([
                'leaderboard_source_id',
                'mode_id'
            ]);
            
            $table->foreign('leaderboard_source_id')
                ->references('id')
                ->on('leaderboard_sources')
                ->onDelete('cascade')
                ->onUpdate('cascade');
                
            $table->foreign('mode_id')
                ->references('id')
                ->on('modes')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
        
        Artisan::call('db:seed', [
            '--class' => 'ModeMatchesSeeder',
            '--force' => true 
        ]);
        
        
        /* ---------- Seeded Types ---------- */
    
        Schema::create('seeded_type_matches', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->smallInteger('leaderboard_source_id');
            $table->smallInteger('seeded_type_id');
            $table->text('match_regex');
            $table->smallInteger('sort_order');
            $table->timestamps();
            
            $table->unique([
                'leaderboard_source_id',
                'seeded_type_id'
            ]);
            
            $table->foreign('leaderboard_source_id')
                ->references('id')
                ->on('leaderboard_sources')
                ->onDelete('cascade')
                ->onUpdate('cascade');
                
            $table->foreign('seeded_type_id')
                ->references('id')
                ->on('seeded_types')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
        
        Artisan::call('db:seed', [
            '--class' => 'SeededTypeMatchesSeeder',
            '--force' => true 
        ]);
        
        
        /* ---------- Multiplayer Types ---------- */
    
        Schema::create('multiplayer_type_matches', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->smallInteger('leaderboard_source_id');
            $table->smallInteger('multiplayer_type_id');
            $table->text('match_regex');
            $table->smallInteger('sort_order');
            $table->timestamps();
            
            $table->unique([
                'leaderboard_source_id',
                'multiplayer_type_id'
            ]);
            
            $table->foreign('leaderboard_source_id')
                ->references('id')
                ->on('leaderboard_sources')
                ->onDelete('cascade')
                ->onUpdate('cascade');
                
            $table->foreign('multiplayer_type_id')
                ->references('id')
                ->on('multiplayer_types')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
        
        Artisan::call('db:seed', [
            '--class' => 'MultiplayerTypeMatchesSeeder',
            '--force' => true 
        ]);
        

        /* ---------- Soundtracks ---------- */
    
        Schema::create('soundtrack_matches', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->smallInteger('leaderboard_source_id');
            $table->smallInteger('soundtrack_id');
            $table->text('match_regex');
            $table->smallInteger('sort_order');
            $table->timestamps();
            
            $table->unique([
                'leaderboard_source_id',
                'soundtrack_id'
            ]);
            
            $table->foreign('leaderboard_source_id')
                ->references('id')
                ->on('leaderboard_sources')
                ->onDelete('cascade')
                ->onUpdate('cascade');
                
            $table->foreign('soundtrack_id')
                ->references('id')
                ->on('soundtracks')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
        
        Artisan::call('db:seed', [
            '--class' => 'SoundtrackMatchesSeeder',
            '--force' => true 
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('leaderboard_type_matches');
        Schema::dropIfExists('character_matches');
        Schema::dropIfExists('release_matches');
        Schema::dropIfExists('mode_matches');
        Schema::dropIfExists('seeded_type_matches');
        Schema::dropIfExists('multiplayer_type_matches');
        Schema::dropIfExists('soundtrack_matches');
    }
}
