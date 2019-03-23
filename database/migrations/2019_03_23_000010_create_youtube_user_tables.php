<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateYoutubeUserTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('youtube_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('external_id')->unique();
            $table->string('etag');
            $table->string('title');
            $table->text('description');
            $table->text('thumbnail_url');
            $table->timestamps();
        });
        
        Schema::create('youtube_user_tokens', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('youtube_user_id');
            $table->text('token');
            $table->text('refresh_token');
            $table->timestamp('expires')->nullable();
            $table->timestamp('created');
            $table->timestamp('expired')->nullable();
            
            $table->foreign('youtube_user_id')
                ->references('id')
                ->on('youtube_users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            
            $table->unique([
                'youtube_user_id',
                'token'
            ]);
        });
        
        Schema::table('users', function (Blueprint $table) {            
            $table->integer('youtube_user_id')->nullable();
            
            $table->foreign('youtube_user_id')
                ->references('id')
                ->on('youtube_users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
                
            $table->index('youtube_user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {            
        Schema::drop('youtube_users');
        
        Schema::drop('youtube_user_tokens');
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('youtube_user_id');
        });
    }
}
