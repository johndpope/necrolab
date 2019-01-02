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
use Illuminate\Support\Facades\DB;
use App\LeaderboardSources;

class CreatePartition implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;
    
    /**
     * The date that this partition will be created for.
     *
     * @var \DateTime
     */
    protected $date;
    
    /**
     * The instance of LeaderboardSources that this partition will be created for.
     *
     * @var \App\LeaderboardSources;
     */
    protected $leaderboard_source;

    /**
     * Create a new job instance.
     *
     * @param \App\LeaderboardSources $leaderboard_source
     * @param \DateTime $date
     * @return void
     */
    public function __construct(LeaderboardSources $leaderboard_source, DateTime $date) {
        $this->leaderboard_source = $leaderboard_source;
    
        $this->date = $date;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        $date_formatted = $this->date->format('Y_m');
        
        $table_short_name = "daily_ranking_entries_{$date_formatted}";
        
        $table_full_name = "{$this->leaderboard_source->name}.{$table_short_name}";
        
        Schema::create($table_full_name, function (Blueprint $table) {
            $table->integer('daily_ranking_id');
            $table->integer('player_id');
            $table->integer('rank');
            $table->smallInteger('first_place_ranks');
            $table->smallInteger('top_5_ranks');
            $table->smallInteger('top_10_ranks');
            $table->smallInteger('top_20_ranks');
            $table->smallInteger('top_50_ranks');
            $table->smallInteger('top_100_ranks');
            $table->smallInteger('total_dailies');
            $table->smallInteger('total_wins');
            $table->smallInteger('sum_of_ranks');
            $table->binary('details');

            $table->primary([
                'daily_ranking_id',
                'player_id'
            ]);
        });
        
        DB::statement("
            ALTER TABLE {$table_full_name}
                ADD CONSTRAINT {$this->leaderboard_source->name}_{$table_short_name}_daily_ranking_id_foreign
                    FOREIGN KEY (daily_ranking_id) REFERENCES {$this->leaderboard_source->name}.daily_rankings (id) ON DELETE CASCADE,
                ADD CONSTRAINT {$this->leaderboard_source->name}_{$table_short_name}_player_id_foreign
                    FOREIGN KEY (player_id) REFERENCES {$this->leaderboard_source->name}.players (id) ON DELETE CASCADE;
        ");
    }
}
