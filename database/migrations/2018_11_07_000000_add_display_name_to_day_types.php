<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddDisplayNameToDayTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {        
        Schema::table('daily_ranking_day_types', function (Blueprint $table) {
            $table->string('display_name')->nullable();
        });
        
        DB::update("
            UPDATE daily_ranking_day_types
            SET display_name = '30 Days'
            WHERE name = 30
        ");
        
        DB::update("
            UPDATE daily_ranking_day_types
            SET display_name = '100 Days'
            WHERE name = 100
        ");
        
        DB::update("
            UPDATE daily_ranking_day_types
            SET display_name = 'All Time'
            WHERE name = 0
        ");
        
        Schema::table('daily_ranking_day_types', function (Blueprint $table) {
            $table->string('display_name')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('daily_ranking_day_types', function (Blueprint $table) {            
            $table->dropColumn('display_name');
        });        
    }
}
