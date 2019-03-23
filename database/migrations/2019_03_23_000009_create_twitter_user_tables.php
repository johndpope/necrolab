<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateTwitterUserTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('twitter_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('external_id')->unique();
            $table->string('nickname');
            $table->string('name');
            $table->text('description');
            $table->text('avatar_url');
            $table->integer('followers_count');
            $table->integer('friends_count');
            $table->integer('statuses_count');
            $table->smallInteger('verified');
            $table->timestamps();
        });
        
        Schema::create('twitter_user_tokens', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('twitter_user_id');
            $table->text('identifier');
            $table->text('secret')->nullable();
            $table->timestamp('expires')->nullable();
            $table->timestamp('created');
            $table->timestamp('expired')->nullable();
            
            $table->foreign('twitter_user_id')
                ->references('id')
                ->on('twitter_users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            
            $table->unique([
                'twitter_user_id',
                'identifier'
            ]);
        });
        
        Schema::table('users', function (Blueprint $table) {            
            $table->integer('twitter_user_id')->nullable();
            
            $table->foreign('twitter_user_id')
                ->references('id')
                ->on('twitter_users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
                
            $table->index('twitter_user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {            
        Schema::drop('twitter_users');
        
        Schema::drop('twitter_user_tokens');
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('twitter_user_id');
        });
    }
}
