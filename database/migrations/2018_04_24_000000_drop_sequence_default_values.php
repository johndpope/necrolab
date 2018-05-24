<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropSequenceDefaultValues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('leaderboards', function (Blueprint $table) {
            $table->integer('leaderboard_id')->default(NULL)->change();
        });
        
        Schema::table('leaderboard_snapshots', function (Blueprint $table) {
            $table->integer('leaderboard_snapshot_id')->default(NULL)->change();
        });
        
        Schema::table('leaderboard_entry_details', function (Blueprint $table) {
            $table->smallInteger('leaderboard_entry_details_id')->default(NULL)->change();
        });
        
        Schema::table('power_rankings', function (Blueprint $table) {
            $table->integer('power_ranking_id')->default(NULL)->change();
        });
        
        Schema::table('daily_rankings', function (Blueprint $table) {
            $table->integer('daily_ranking_id')->default(NULL)->change();
        });
        
        Schema::table('run_results', function (Blueprint $table) {
            $table->integer('run_result_id')->default(NULL)->change();
        });
        
        Schema::table('steam_replay_versions', function (Blueprint $table) {
            $table->integer('steam_replay_version_id')->default(NULL)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
    }
}