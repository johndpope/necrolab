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
                ->references('release_id')
                ->on('releases')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            
            $table->foreign('mode_id')
                ->references('mode_id')
                ->on('modes')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
        
        Artisan::call('db:seed', [
            '--class' => 'ReleaseModesSeeder',
            '--force' => true 
        ]);
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
