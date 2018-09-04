<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddPersonanameSearchIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {    
        DB::statement("
            ALTER TABLE steam_users 
            ADD COLUMN personaname_search_index tsvector
        ");
        
        DB::statement("
            CREATE INDEX idx_su_personaname_search_index 
            ON steam_users 
            USING GIN (personaname_search_index);
        ");
        
        DB::update("
            UPDATE steam_users
            SET personaname_search_index = to_tsvector(personaname)
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('steam_users', function (Blueprint $table) {
            $table->dropColumn('personaname_search_index');
            $table->dropIndex("idx_su_personaname_search_index");
        });
    }
}
