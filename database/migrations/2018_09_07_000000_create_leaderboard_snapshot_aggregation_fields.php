<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateLeaderboardSnapshotAggregationFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {    
        Schema::table('leaderboard_snapshots', function (Blueprint $table) {
            $table->integer('players')->nullable();
            $table->integer('score')->nullable();
            $table->double('time', 16, 6)->nullable();
            $table->integer('win_count')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('leaderboard_snapshots', function (Blueprint $table) {
            $table->dropColumn('players');
            $table->dropColumn('score');
            $table->dropColumn('time');
            $table->dropColumn('win_count');
        });
    }
}
