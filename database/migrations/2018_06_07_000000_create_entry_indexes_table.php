<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntryIndexesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entry_indexes', function (Blueprint $table) {
            $table->string('name');
            $table->string('sub_name');
            $table->binary('data');
            
            $table->primary([
                'name', 
                'sub_name'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('entry_indexes');
    }
}
