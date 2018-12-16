<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateLeaderboardDetailsColumnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leaderboard_details_columns', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name', 100);
            $table->string('display_name', 255);
            $table->smallInteger('data_type_id');
            $table->smallInteger('sort_order');
            $table->smallInteger('enabled');
            
            $table->foreign('data_type_id')
                ->references('id')
                ->on('data_types')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            
            $table->index('data_type_id');
        });
        
        Schema::table('leaderboard_types', function (Blueprint $table) {
            $table->integer('leaderboard_details_column_id')->nullable();
            
            $table->foreign('leaderboard_details_column_id')
                ->references('id')
                ->on('leaderboard_details_columns')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            
            $table->index('leaderboard_details_column_id');
        });
        
        Artisan::call('db:seed', [
            '--class' => 'LeaderboardDetailsColumnsSeeder',
            '--force' => true 
        ]);
        
        Schema::table('leaderboard_types', function (Blueprint $table) {
            $table->integer('leaderboard_details_column_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {    
        Schema::table('leaderboard_types', function (Blueprint $table) {
            $table->dropColumn('leaderboard_details_column_id');
        });
    
        Schema::dropIfExists('leaderboard_details_columns');
    }
}
