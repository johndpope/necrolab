<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateSeedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {    
        Schema::create('seeds', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 100)->unique();
        });
        
        Schema::table('steam_replays', function (Blueprint $table) {
            $table->bigInteger('seed_id')->nullable();
            
            $table->foreign('seed_id')
                ->references('id')
                ->on('seeds')
                ->onDelete('cascade');
            
            $table->index('seed_id');
        });
        
        DB::insert("
            INSERT INTO seeds (name)
            SELECT CAST(seed AS character varying)
            FROM steam_replays
            WHERE seed IS NOT NULL
            ON CONFLICT (name) DO NOTHING
        ");
        
        DB::update("
            UPDATE steam_replays AS sr
            SET seed_id = s.id
            FROM seeds s
            WHERE s.name = CAST(sr.seed AS character varying)
        ");
        
        Schema::table('steam_replays', function (Blueprint $table) {            
            $table->dropColumn('seed');
        });
        
        Schema::table('seeds', function (Blueprint $table) {            
            $table->bigInteger('id')->default(NULL)->change();
        });
        
        DB::statement("
            ALTER SEQUENCE seeds_id_seq
            RENAME TO seeds_seq
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('seeds');
    }
}