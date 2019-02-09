<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddImportFieldColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {        
        Schema::table('leaderboard_details_columns', function (Blueprint $table) {
            $table->string('import_field')->nullable();
        });
        
        DB::update("
            UPDATE leaderboard_details_columns
            SET import_field = 'score'
            WHERE name = 'score'
        ");
        
        DB::update("
            UPDATE leaderboard_details_columns
            SET import_field = 'time'
            WHERE name = 'time'
        ");
        
        DB::update("
            UPDATE leaderboard_details_columns
            SET import_field = 'win_count'
            WHERE name = 'win_count'
        ");
        
        Schema::table('leaderboard_details_columns', function (Blueprint $table) {
            $table->string('import_field')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('leaderboard_details_columns', function (Blueprint $table) {            
            $table->dropColumn('import_field');
        });    
    }
}
