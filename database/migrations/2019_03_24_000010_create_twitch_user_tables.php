<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateTwitchUserTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('twitch_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('external_id')->unique();
            $table->string('username');
            $table->string('user_display_name');
            $table->text('logo_url');
            $table->text('bio');
            $table->smallInteger('partnered');
            $table->timestamps();
        });
        
        Schema::create('twitch_user_tokens', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('twitch_user_id');
            $table->text('token');
            $table->text('refresh_token');
            $table->timestamp('expires')->nullable();
            $table->timestamp('created');
            $table->timestamp('expired')->nullable();
            
            $table->foreign('twitch_user_id')
                ->references('id')
                ->on('twitch_users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            
            $table->unique([
                'twitch_user_id',
                'token'
            ]);
        });
        
        Schema::table('users', function (Blueprint $table) {            
            $table->integer('twitch_user_id')->nullable();
            
            $table->foreign('twitch_user_id')
                ->references('id')
                ->on('twitch_users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
                
            $table->index('twitch_user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {            
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('twitch_user_id');
        });
        
        Schema::drop('twitch_user_tokens');
        
        Schema::drop('twitch_users');
    }
}
