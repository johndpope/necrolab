<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateReleaseModesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('release_modes', function (Blueprint $table) {
            $table->smallInteger('release_id');
            $table->smallInteger('mode_id');
            
            $table->primary([
                'release_id',
                'mode_id'
            ]);
            
            $table->foreign('release_id')
                ->references('id')
                ->on('releases')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            
            $table->foreign('mode_id')
                ->references('id')
                ->on('modes')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {    
        Schema::dropIfExists('release_modes');
    }
}
