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
    public function up()
    {
        Schema::create('soundtracks', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name', 100);
            $table->string('display_name', 255);
        });
        
        Artisan::call('db:seed', [
            '--class' => 'SoundtracksSeeder',
            '--force' => true 
        ]);
        
        Schema::table('leaderboards', function (Blueprint $table) {
            $table->integer('soundtrack_id')->nullable();
            
            $table->foreign('soundtrack_id')
                ->references('id')
                ->on('soundtracks')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            
            $table->index('soundtrack_id');
        });
        
        DB::update("
            UPDATE leaderboards
            SET soundtrack_id = 1
            WHERE is_custom = 0
        ");
        
        DB::update("
            UPDATE leaderboards
            SET soundtrack_id = 2
            WHERE is_custom = 1
        ");
        
        Schema::table('leaderboards', function (Blueprint $table) {
            $table->integer('soundtrack_id')->nullable(false)->change();
            
            $table->dropColumn('is_custom');
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
            $table->dropColumn('soundtrack_id');
        });
    
        Schema::dropIfExists('soundtracks');
    }
}
