<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSoundtracksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('soundtracks', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name', 100);
            $table->string('display_name', 255);
            $table->smallInteger('is_default');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('soundtracks');
    }
}
