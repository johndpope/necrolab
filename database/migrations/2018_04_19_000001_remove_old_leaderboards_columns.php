<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveOldLeaderboardsColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('leaderboards', function (Blueprint $table) {
            $table->text('url')->nullable()->change();
            
            $table->dropColumn('is_speedrun');
            $table->dropColumn('is_daily');
            $table->dropColumn('is_score_run');
            $table->dropColumn('is_deathless');
            $table->dropColumn('is_dev');
            $table->dropColumn('is_prod');
            $table->dropColumn('is_power_ranking');
            $table->dropColumn('is_daily_ranking');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {}
}