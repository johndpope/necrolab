<?php

namespace App\Jobs\Rankings\Daily\Entries;

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
        
        Schema::create("daily_ranking_entries_{$date_formatted}", function (Blueprint $table) {
            $table->integer('daily_ranking_id');
            $table->integer('steam_user_id');
            $table->smallInteger('first_place_ranks');
            $table->smallInteger('top_5_ranks');
            $table->smallInteger('top_10_ranks');
            $table->smallInteger('top_20_ranks');
            $table->smallInteger('top_50_ranks');
            $table->smallInteger('top_100_ranks');
            $table->decimal('total_points');
            $table->smallInteger('total_dailies');
            $table->smallInteger('total_wins');
            $table->smallInteger('sum_of_ranks');
            $table->smallInteger('total_score');
            $table->integer('rank');

            $table->foreign('daily_ranking_id')
                ->references('daily_ranking_id')
                ->on('daily_rankings')
                ->onDelete('cascade');
                
            $table->foreign('steam_user_id')
                ->references('steam_user_id')
                ->on('steam_users')
                ->onDelete('cascade');

            $table->primary([
                'daily_ranking_id',
                'steam_user_id'
            ]);
        });
    }
}