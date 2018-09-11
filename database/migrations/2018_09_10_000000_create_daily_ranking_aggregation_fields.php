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
            $table->integer('players')->default(0);
            $table->integer('first_place_ranks')->default(0);
            $table->integer('top_5_ranks')->default(0);
            $table->integer('top_10_ranks')->default(0);
            $table->integer('top_20_ranks')->default(0);
            $table->integer('top_50_ranks')->default(0);
            $table->integer('top_100_ranks')->default(0);
            $table->double('total_points')->default(0.0);
            $table->integer('total_dailies')->default(0);
            $table->integer('total_wins')->default(0);
            $table->integer('sum_of_ranks')->default(0);
            $table->integer('total_score')->default(0);
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
