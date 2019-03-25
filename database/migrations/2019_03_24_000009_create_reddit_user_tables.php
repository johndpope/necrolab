<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateRedditUserTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('reddit_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('external_id')->unique();
            $table->string('username');
            $table->integer('comment_karma');
            $table->integer('link_karma');
            $table->smallInteger('over_18');
            $table->smallInteger('has_gold');
            $table->smallInteger('is_employee');
            $table->timestamp('reddit_created');
            $table->timestamps();
        });
        
        Schema::create('reddit_user_tokens', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('reddit_user_id');
            $table->text('token');
            $table->text('refresh_token');
            $table->timestamp('expires')->nullable();
            $table->timestamp('created');
            $table->timestamp('expired')->nullable();
            
            $table->foreign('reddit_user_id')
                ->references('id')
                ->on('reddit_users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            
            $table->unique([
                'reddit_user_id',
                'token'
            ]);
        });
        
        Schema::table('users', function (Blueprint $table) {            
            $table->integer('reddit_user_id')->nullable();
            
            $table->foreign('reddit_user_id')
                ->references('id')
                ->on('reddit_users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
                
            $table->index('reddit_user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {         
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('reddit_user_id');
        });
        
        Schema::drop('reddit_user_tokens');
        
        Schema::drop('reddit_users');
    }
}
