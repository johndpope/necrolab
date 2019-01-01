<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class RenameDailyRankingDayTypeIdColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {    
        Schema::table('daily_ranking_day_types', function (Blueprint $table) {
            $table->renameColumn('daily_ranking_day_type_id', 'id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('daily_ranking_day_types', function (Blueprint $table) {
            $table->renameColumn('id', 'daily_ranking_day_type_id');
        });
    }
}
