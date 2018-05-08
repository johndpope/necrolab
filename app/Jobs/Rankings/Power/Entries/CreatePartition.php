<?php

namespace App\Jobs\Rankings\Power\Entries;

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
        
        Schema::create("power_ranking_entries_{$date_formatted}", function (Blueprint $table) {
            $table->integer('power_ranking_id');
            $table->integer('steam_user_id');
            $table->jsonb('characters');
            $table->integer('score_rank')->nullable();
            $table->integer('deathless_rank')->nullable();
            $table->integer('speed_rank')->nullable();
            $table->integer('rank');

            $table->foreign('power_ranking_id')
                ->references('power_ranking_id')
                ->on('power_rankings')
                ->onDelete('cascade');
                
            $table->foreign('steam_user_id')
                ->references('steam_user_id')
                ->on('steam_users')
                ->onDelete('cascade');

            $table->primary([
                'power_ranking_id',
                'steam_user_id'
            ]);
        });
    }
}