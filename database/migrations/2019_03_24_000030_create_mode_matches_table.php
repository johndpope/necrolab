<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModeMatchesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('mode_matches', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->smallInteger('leaderboard_source_id');
            $table->smallInteger('mode_id');
            $table->text('match_regex');
            $table->smallInteger('sort_order');
            $table->timestamps();
            
            $table->unique([
                'leaderboard_source_id',
                'mode_id'
            ]);
            
            $table->foreign('leaderboard_source_id')
                ->references('id')
                ->on('leaderboard_sources')
                ->onDelete('cascade')
                ->onUpdate('cascade');
                
            $table->foreign('mode_id')
                ->references('id')
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
    public function down() {
        Schema::dropIfExists('mode_matches');
    }
}
