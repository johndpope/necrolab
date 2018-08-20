<?php

namespace App\Jobs\Leaderboards\Entries;

use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class CreatePartition implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;
    
    protected $date;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(DateTime $date) {
        $this->date = $date;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        $date_formatted = $this->date->format('Y_m');
        
        Schema::create("leaderboard_entries_{$date_formatted}", function (Blueprint $table) {
            $table->integer('leaderboard_snapshot_id');
            $table->integer('steam_user_pb_id');
            $table->integer('rank');

            $table->foreign('leaderboard_snapshot_id')
                ->references('leaderboard_snapshot_id')
                ->on('leaderboard_snapshots')
                ->onDelete('cascade');
                
            $table->foreign('steam_user_pb_id')
                ->references('steam_user_pb_id')
                ->on('steam_user_pbs')
                ->onDelete('cascade');

            $table->primary(['leaderboard_snapshot_id', 'steam_user_pb_id']);
        });
    }
}
