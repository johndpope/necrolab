<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateMultiplayerTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('multiplayer_types', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name', 100);
            $table->string('display_name', 255);
        });
        
        Artisan::call('db:seed', [
            '--class' => 'MultiplayerTypesSeeder',
            '--force' => true 
        ]);
        
        Schema::table('leaderboards', function (Blueprint $table) {
            $table->integer('multiplayer_type_id')->nullable();
            
            $table->foreign('multiplayer_type_id')
                ->references('id')
                ->on('multiplayer_types')
                ->onDelete('cascade')
                ->onUpdate('cascade');
                
            $table->index('multiplayer_type_id');
        });
        
        DB::update("
            UPDATE leaderboards
            SET multiplayer_type_id = 1
            WHERE is_co_op = 0
        ");
        
        DB::update("
            UPDATE leaderboards
            SET multiplayer_type_id = 2
            WHERE is_co_op = 1
        ");
        
        Schema::table('leaderboards', function (Blueprint $table) {
            $table->integer('multiplayer_type_id')->nullable(false)->change();
            
            $table->dropColumn('is_co_op');
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
            $table->dropColumn('multiplayer_type_id');
        });
    
        Schema::dropIfExists('multiplayer_types');
    }
}
