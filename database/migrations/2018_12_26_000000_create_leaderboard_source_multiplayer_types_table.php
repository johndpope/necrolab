<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateLeaderboardSourceMultiplayerTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leaderboard_source_multiplayer_types', function (Blueprint $table) {
            $table->smallInteger('leaderboard_source_id');
            $table->smallInteger('multiplayer_type_id');
            
            $table->primary([
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
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {    
        Schema::dropIfExists('leaderboard_source_multiplayer_types');
    }
}
