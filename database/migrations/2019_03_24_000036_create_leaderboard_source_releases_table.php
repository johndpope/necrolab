<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateLeaderboardSourceReleasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leaderboard_source_releases', function (Blueprint $table) {
            $table->smallInteger('leaderboard_source_id');
            $table->smallInteger('release_id');
            
            $table->primary([
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
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {    
        Schema::dropIfExists('leaderboard_source_releases');
    }
}
