<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Components\DateIncrementor;
use App\Components\CallbackHandler;

class RemoveLeaderboardEntriesIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {        
        $callback_handler = new CallbackHandler();

        $callback_handler->setCallback(function(DateTime $date) {
            $date_formatted = $date->format('Y_m');
        
            Schema::table("leaderboard_entries_{$date_formatted}", function (Blueprint $table) use($date_formatted) {
                $table->dropIndex("idx_leaderboard_entries_{$date_formatted}_steam_user_pb_id");
            });
        });
    
        $date_incrementor = new DateIncrementor(
            new DateTime('2014-07-01'),
            new DateTime(),
            new DateInterval('P1M')
        );
        
        $date_incrementor->run($callback_handler);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {}
}
