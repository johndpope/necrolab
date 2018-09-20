<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class LinkUsersToSteamUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {                
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_email_unique');
        });
    
        Schema::table('users', function (Blueprint $table) {            
            DB::statement("
                CREATE UNIQUE INDEX users_email_unique 
                ON users (email)
                WHERE email IS NOT NULL
            ");
            
            $table->string('email')->nullable()->change();
            $table->string('name')->nullable()->change();
            $table->string('password')->nullable()->change();
        
            $table->text('website')->nullable();
            
            
            /* ---------- Steam ---------- */
            
            $table->integer('steam_user_id')->nullable();
            
            $table->foreign('steam_user_id')
                ->references('steam_user_id')
                ->on('steam_users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            
            
            /* ---------- Discord ---------- */
            
            $table->integer('discord_user_id')->nullable();
            
            $table->foreign('discord_user_id')
                ->references('discord_user_id')
                ->on('discord_users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            
            $table->index('discord_user_id');
            
            
            /* ---------- Mixer ---------- */
            
            $table->integer('mixer_user_id')->nullable();
            
            $table->foreign('mixer_user_id')
                ->references('id')
                ->on('mixer_users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
                
            $table->index('mixer_user_id');
            
            
            /* ---------- Reddit ---------- */
            
            $table->integer('reddit_user_id')->nullable();
            
            $table->foreign('reddit_user_id')
                ->references('reddit_user_id')
                ->on('reddit_users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
                
            $table->index('reddit_user_id');
            
            
            /* ---------- Twitch ---------- */
            
            $table->integer('twitch_user_id')->nullable();
            
            $table->foreign('twitch_user_id')
                ->references('twitch_user_id')
                ->on('twitch_users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
                
            $table->index('twitch_user_id');
            
            
            /* ---------- Twitter ---------- */
            
            $table->integer('twitter_user_id')->nullable();
            
            $table->foreign('twitter_user_id')
                ->references('twitter_user_id')
                ->on('twitter_users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            
            $table->index('twitter_user_id');
            
            
            /* ---------- YouTube ---------- */
            
            $table->integer('youtube_user_id')->nullable();
            
            $table->foreign('youtube_user_id')
                ->references('youtube_user_id')
                ->on('youtube_users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            
            $table->index('youtube_user_id');
        });
        
        Schema::table('users', function (Blueprint $table) {
            DB::statement("
                CREATE UNIQUE INDEX idx_su_steam_user_id_uq 
                ON users (steam_user_id)
                WHERE steam_user_id IS NOT NULL
            ");
        });
        
        
        /* ---------- Drop columns from steam_users ---------- */
        
        Schema::table('steam_users', function (Blueprint $table) {
            $table->dropIndex("idx_su_beampro_user_id");
            $table->dropIndex("idx_su_discord_user_id");
            $table->dropIndex("idx_su_reddit_user_id");
            $table->dropIndex("idx_su_twitch_user_id");
            $table->dropIndex("idx_su_twitter_user_id");
            $table->dropIndex("idx_su_youtube_user_id");
        
            $table->dropColumn('website');
            $table->dropColumn('twitch_user_id');
            $table->dropColumn('reddit_user_id');
            $table->dropColumn('discord_user_id');
            $table->dropColumn('youtube_user_id');
            $table->dropColumn('twitter_user_id');
            $table->dropColumn('beampro_user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::statement("
            DROP INDEX IF EXISTS users_email_unique
        ");
    
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex("idx_su_steam_user_id_uq");
            
            $table->string('email')->change();
            $table->string('name')->change();
            $table->string('password')->change();
            
            $table->unique('email');
        
            $table->dropColumn('website');
            $table->dropColumn('steam_user_id');
            $table->dropColumn('discord_user_id');
            $table->dropColumn('mixer_user_id');
            $table->dropColumn('reddit_user_id');
            $table->dropColumn('twitch_user_id');
            $table->dropColumn('twitter_user_id');
            $table->dropColumn('youtube_user_id');
        });
    }
}
