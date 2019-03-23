<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateModesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {    
        Schema::create('modes', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name', 100)->unique();
            $table->string('display_name', 255);
            $table->smallInteger('is_default');
            $table->smallInteger('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('modes');
    }
}
