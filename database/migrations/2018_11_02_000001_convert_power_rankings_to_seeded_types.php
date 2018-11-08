<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ConvertPowerRankingsToSeededTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {        
        Schema::table('power_rankings', function (Blueprint $table) {
            $table->integer('seeded_type_id')->nullable();
            
            $table->foreign('seeded_type_id')
                ->references('id')
                ->on('seeded_types')
                ->onDelete('cascade')
                ->onUpdate('cascade');
                
            $table->index('seeded_type_id');
        });
        
        DB::update("
            UPDATE power_rankings
            SET seeded_type_id = 1
            WHERE seeded = 0
        ");
        
        DB::update("
            UPDATE power_rankings
            SET seeded_type_id = 2
            WHERE seeded = 1
        ");
        
        Schema::table('power_rankings', function (Blueprint $table) {
            $table->integer('seeded_type_id')->nullable(false)->change();
            
            $table->dropColumn('seeded');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('power_rankings', function (Blueprint $table) {            
            $table->dropColumn('seeded_type_id');
        });        
    }
}
