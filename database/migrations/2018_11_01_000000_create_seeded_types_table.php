<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSeededTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seeded_types', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name', 100);
            $table->string('display_name', 255);
        });
        
        Artisan::call('db:seed', [
            '--class' => 'SeededTypesSeeder',
            '--force' => true 
        ]);
        
        Schema::table('leaderboards', function (Blueprint $table) {
            $table->integer('seeded_type_id')->nullable();
            
            $table->foreign('seeded_type_id')
                ->references('id')
                ->on('seeded_types')
                ->onDelete('cascade')
                ->onUpdate('cascade');
                
            $table->index('seeded_type_id');
        });
        
        DB::update("
            UPDATE leaderboards
            SET seeded_type_id = 1
            WHERE is_seeded = 0
        ");
        
        DB::update("
            UPDATE leaderboards
            SET seeded_type_id = 2
            WHERE is_seeded = 1
        ");
        
        Schema::table('leaderboards', function (Blueprint $table) {
            $table->integer('seeded_type_id')->nullable(false)->change();
            
            $table->dropColumn('is_seeded');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leaderboards', function (Blueprint $table) {            
            $table->dropColumn('seeded_type_id');
        });
        
        Schema::dropIfExists('seeded_types');        
    }
}
