<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateReleasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {    
        Schema::create('releases', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name', 100)->unique();
            $table->string('display_name', 255);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->smallInteger('win_zone')->nullable();
            $table->smallInteger('win_level')->nullable();
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
        Schema::dropIfExists('releases');
    }
}
