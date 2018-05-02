<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class ChangeSteamReplaysPrimaryKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {    
        Schema::table('steam_replays', function (Blueprint $table) {
            $table->integer('steam_user_pb_id')->nullable();
            
            $table->foreign('steam_user_pb_id')
                ->references('steam_user_pb_id')
                ->on('steam_user_pbs')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
        
        DB::update("
            UPDATE steam_replays AS sr
            SET steam_user_pb_id = sup.steam_user_pb_id
            FROM steam_user_pbs sup
            WHERE sup.steam_replay_id = sr.steam_replay_id
        ");
        
        DB::delete("
            DELETE FROM steam_replays 
            WHERE steam_user_pb_id IS NULL
        ");
        
        Schema::table('steam_user_pbs', function (Blueprint $table) {            
            $table->dropIndex('idx_steam_user_pbs_steam_replay_id');
            
            $table->dropForeign('fk_steam_user_pbs_steam_replay_id');
            
            $table->dropColumn('steam_replay_id');
        });
        
        Schema::table('steam_replays', function (Blueprint $table) {
            $table->integer('steam_user_pb_id')->nullable(false)->change();
            
            $table->dropColumn('steam_replay_id');
            
            $table->primary('steam_user_pb_id');
            
            $table->dropUnique('uq_sr_ugcid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
    }
}