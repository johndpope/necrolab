<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateLeaderboardSourceCharactersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leaderboard_source_characters', function (Blueprint $table) {
            $table->smallInteger('leaderboard_source_id');
            $table->smallInteger('character_id');
            
            $table->primary([
                'leaderboard_source_id',
                'character_id'
            ]);
            
            $table->foreign('leaderboard_source_id')
                ->references('id')
                ->on('leaderboard_sources')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            
            $table->foreign('character_id')
                ->references('character_id')
                ->on('characters')
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
        Schema::dropIfExists('leaderboard_source_characters');
    }
}
