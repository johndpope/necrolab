<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeaderboardTypeMatchesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('leaderboard_type_matches', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->smallInteger('leaderboard_source_id');
            $table->smallInteger('leaderboard_type_id');
            $table->text('match_regex');
            $table->smallInteger('sort_order');
            $table->timestamps();
            
            $table->unique([
                'leaderboard_source_id',
                'leaderboard_type_id'
            ]);
            
            $table->foreign('leaderboard_source_id')
                ->references('id')
                ->on('leaderboard_sources')
                ->onDelete('cascade')
                ->onUpdate('cascade');
                
            $table->foreign('leaderboard_type_id')
                ->references('id')
                ->on('leaderboard_types')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {            
        Schema::dropIfExists('leaderboard_type_matches');
    }
}
