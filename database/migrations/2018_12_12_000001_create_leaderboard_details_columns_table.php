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
            $table->string('import_field');
            
            $table->foreign('data_type_id')
                ->references('id')
                ->on('data_types')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            
            $table->index('data_type_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {        
        Schema::dropIfExists('leaderboard_details_columns');
    }
}
