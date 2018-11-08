<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddUrlName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {                
        Schema::table('leaderboards', function (Blueprint $table) {            
            $table->string('url_name')->nullable();
            
            $table->index('url_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('leaderboards', function (Blueprint $table) {            
            $table->dropColumn('url_name');
        });
    }
}
