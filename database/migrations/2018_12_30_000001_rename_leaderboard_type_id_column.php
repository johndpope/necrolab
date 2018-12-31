<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class RenameLeaderboardTypeIdColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {    
        Schema::table('leaderboard_types', function (Blueprint $table) {
            $table->renameColumn('leaderboard_type_id', 'id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('leaderboard_types', function (Blueprint $table) {
            $table->renameColumn('id', 'leaderboard_type_id');
        });
    }
}
