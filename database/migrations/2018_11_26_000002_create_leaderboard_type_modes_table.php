<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateLeaderboardTypeModesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leaderboard_type_modes', function (Blueprint $table) {
            $table->smallInteger('leaderboard_type_id');
            $table->smallInteger('mode_id');
            
            $table->primary([
                'leaderboard_type_id',
                'mode_id'
            ]);
            
            $table->foreign('leaderboard_type_id')
                ->references('leaderboard_type_id')
                ->on('leaderboard_types')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            
            $table->foreign('mode_id')
                ->references('mode_id')
                ->on('modes')
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
        Schema::dropIfExists('leaderboard_type_modes');
    }
}
