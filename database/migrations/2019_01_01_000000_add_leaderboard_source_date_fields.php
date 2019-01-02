<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddLeaderboardSourceDateFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {        
        Schema::table('leaderboard_sources', function (Blueprint $table) {
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
        });
        
        $current_date = new \DateTime();
        
        DB::update("
            UPDATE leaderboard_sources
            SET start_date = :start_date
        ", [
            ":start_date" => $current_date->format('Y-m-d')
        ]);
        
        Schema::table('leaderboard_sources', function (Blueprint $table) {
            $table->date('start_date')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('leaderboard_sources', function (Blueprint $table) {            
            $table->dropColumn('start_date');
            $table->dropColumn('end_date');
        });    
    }
}
