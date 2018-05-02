<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRankingTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::dropIfExists('leaderboard_ranking_types');
        Schema::dropIfExists('ranking_types');
    
        Schema::create('ranking_types', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name', 100);
            $table->string('display_name', 255);
        });
        
        Schema::create('leaderboard_ranking_types', function (Blueprint $table) {          
            $table->smallInteger('leaderboard_id');            
            $table->foreign('leaderboard_id')->references('leaderboard_id')->on('leaderboards');

            $table->smallInteger('ranking_type_id')->index();            
            $table->foreign('ranking_type_id')->references('id')->on('ranking_types');
            
            $table->primary([
                'leaderboard_id', 
                'ranking_type_id'
            ]);
        });

        Artisan::call('db:seed', [
            '--class' => 'RankingTypesSeeder',
            '--force' => true 
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('leaderboard_ranking_types');
        Schema::dropIfExists('ranking_types');
    }
}
