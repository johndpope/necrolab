<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateMixerUserTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('mixer_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('external_id')->unique();
            $table->string('username')->nullable();
            $table->text('avatar_url')->nullable();
            $table->text('description')->nullable();
            $table->text('bio')->nullable();
            $table->text('channel_title')->nullable();
            $table->integer('views')->nullable();
            $table->integer('followers')->nullable();
            $table->timestamp('updated')->nullable();
        });
        
        Schema::create('mixer_user_tokens', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('mixer_user_id');
            $table->text('token');
            $table->text('refresh_token');
            $table->timestamp('expires')->nullable();
            $table->timestamp('created');
            $table->timestamp('expired')->nullable();
            
            $table->foreign('mixer_user_id')
                ->references('id')
                ->on('mixer_users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            
            $table->index('mixer_user_id');
        });
        
        Schema::table('users', function (Blueprint $table) {            
            $table->integer('mixer_user_id')->nullable();
            
            $table->foreign('mixer_user_id')
                ->references('id')
                ->on('mixer_users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
                
            $table->index('mixer_user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {            
        Schema::drop('mixer_users');
        
        Schema::drop('mixer_user_tokens');
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('mixer_user_id');
        });
    }
}
