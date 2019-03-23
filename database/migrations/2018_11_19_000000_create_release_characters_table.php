<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateReleaseCharactersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('release_characters', function (Blueprint $table) {
            $table->smallInteger('release_id');
            $table->smallInteger('character_id');
            
            $table->primary([
                'release_id',
                'character_id'
            ]);
            
            $table->foreign('release_id')
                ->references('release_id')
                ->on('releases')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            
            $table->foreign('character_id')
                ->references('character_id')
                ->on('characters')
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
        Schema::dropIfExists('release_characters');
    }
}
