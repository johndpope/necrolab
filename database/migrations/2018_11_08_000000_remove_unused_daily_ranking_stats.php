<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveUnusedDailyRankingStats extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {        
        Schema::table('daily_rankings', function (Blueprint $table) {
            $table->dropColumn('first_place_ranks');
            $table->dropColumn('top_5_ranks');
            $table->dropColumn('top_10_ranks');
            $table->dropColumn('top_20_ranks');
            $table->dropColumn('top_50_ranks');
            $table->dropColumn('top_100_ranks');
            $table->dropColumn('total_points');
            $table->dropColumn('sum_of_ranks');
            
            $table->bigInteger('total_score')->nullable(false)->default(0)->change();
        }); 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {}
}
