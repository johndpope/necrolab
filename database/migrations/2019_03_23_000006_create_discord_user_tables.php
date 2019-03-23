<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateDiscordUserTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('discord_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('external_id')->unique();
            $table->string('username');
            $table->text('email');
            $table->string('discriminator');
            $table->text('avatar_url')->nullable();
            $table->timestamps();
        });
        
        Schema::create('discord_user_tokens', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('discord_user_id');
            $table->text('token');
            $table->text('refresh_token');
            $table->timestamp('expires')->nullable();
            $table->timestamp('created');
            $table->timestamp('expired')->nullable();
            
            $table->foreign('discord_user_id')
                ->references('id')
                ->on('discord_users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            
            $table->unique([
                'discord_user_id',
                'token'
            ]);
        });
        
        Schema::table('users', function (Blueprint $table) {            
            $table->integer('discord_user_id')->nullable();
            
            $table->foreign('discord_user_id')
                ->references('id')
                ->on('discord_users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
                
            $table->index('discord_user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {            
        Schema::drop('discord_users');
        
        Schema::drop('discord_user_tokens');
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('discord_user_id');
        });
    }
}
