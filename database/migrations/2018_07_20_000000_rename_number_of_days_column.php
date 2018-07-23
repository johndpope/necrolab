<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class RenameNumberOfDaysColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {    
        Schema::table('daily_ranking_day_types', function (Blueprint $table) {
            $table->renameColumn('number_of_days', 'name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('daily_ranking_day_types', function (Blueprint $table) {
            $table->renameColumn('name', 'number_of_days');
        });
    }
}