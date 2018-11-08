<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreatePowerRankingAggregationFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {                
        Schema::table('power_rankings', function (Blueprint $table) {
            $table->integer('players')->default(0);
            $table->binary('categories')->nullable();
            $table->binary('characters')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('power_rankings', function (Blueprint $table) {            
            $table->dropColumn('players');
            $table->dropColumn('categories');
            $table->dropColumn('characters');
        });
    }
}
