<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateLeaderboardTypeDetailsColumnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leaderboard_type_details_columns', function (Blueprint $table) {
            $table->smallInteger('leaderboard_type_id');
            $table->smallInteger('leaderboard_details_column_id');
            
            $table->primary([
                'leaderboard_type_id',
                'leaderboard_details_column_id'
            ]);
            
            $table->foreign('leaderboard_type_id')
                ->references('id')
                ->on('leaderboard_types')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            
            $table->foreign('leaderboard_details_column_id')
                ->references('id')
                ->on('leaderboard_details_columns')
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
        Schema::dropIfExists('leaderboard_type_details_columns');
    }
}
