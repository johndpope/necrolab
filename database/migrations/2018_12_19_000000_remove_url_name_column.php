<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveUrlNameColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {        
        Schema::table('leaderboards', function (Blueprint $table) {
            $table->dropColumn('url_name');
        }); 
        
        Schema::table('leaderboard_sources', function (Blueprint $table) {
            $table->dropColumn('url_name');
        }); 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {}
}
