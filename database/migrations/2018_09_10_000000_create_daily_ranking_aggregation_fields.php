<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateDailyRankingAggregationFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {    
        Schema::table('daily_rankings', function (Blueprint $table) {
            $table->integer('players')->nullable();
            $table->integer('first_place_ranks')->nullable();
            $table->integer('top_5_ranks')->nullable();
            $table->integer('top_10_ranks')->nullable();
            $table->integer('top_20_ranks')->nullable();
            $table->integer('top_50_ranks')->nullable();
            $table->integer('top_100_ranks')->nullable();
            $table->double('total_points')->nullable();
            $table->integer('total_dailies')->nullable();
            $table->integer('total_wins')->nullable();
            $table->integer('sum_of_ranks')->nullable();
            $table->integer('total_score')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('daily_rankings', function (Blueprint $table) {
            $table->dropColumn('players');
            $table->dropColumn('first_place_ranks');
            $table->dropColumn('top_5_ranks');
            $table->dropColumn('top_10_ranks');
            $table->dropColumn('top_20_ranks');
            $table->dropColumn('top_50_ranks');
            $table->dropColumn('top_100_ranks');
            $table->dropColumn('total_points');
            $table->dropColumn('total_dailies');
            $table->dropColumn('total_wins');
            $table->dropColumn('sum_of_ranks');
            $table->dropColumn('total_score');
        });
    }
}
