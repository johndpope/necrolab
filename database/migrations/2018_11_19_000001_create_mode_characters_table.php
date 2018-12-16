<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateModeCharactersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mode_characters', function (Blueprint $table) {
            $table->smallInteger('mode_id');
            $table->smallInteger('character_id');
            
            $table->primary([
                'mode_id',
                'character_id'
            ]);
            
            $table->foreign('mode_id')
                ->references('mode_id')
                ->on('modes')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            
            $table->foreign('character_id')
                ->references('character_id')
                ->on('characters')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
        
        Artisan::call('db:seed', [
            '--class' => 'ModeCharactersSeeder',
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
        Schema::dropIfExists('mode_characters');
    }
}
