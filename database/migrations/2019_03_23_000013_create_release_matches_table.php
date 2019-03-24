<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReleaseMatchesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('release_matches', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->smallInteger('leaderboard_source_id');
            $table->smallInteger('release_id');
            $table->text('match_regex');
            $table->smallInteger('sort_order');
            $table->timestamps();
            
            $table->unique([
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
    public function down() {
        Schema::dropIfExists('release_matches');
    }
}
