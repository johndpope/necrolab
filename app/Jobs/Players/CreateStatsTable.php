<?php

namespace App\Jobs\Players;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use App\LeaderboardSources;

class CreateStatsTable implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 3600;

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
    public function __construct(LeaderboardSources $leaderboard_source) {
        $this->leaderboard_source = $leaderboard_source;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        $table_short_name = "player_stats";

        $table_full_name = "{$this->leaderboard_source->name}.{$table_short_name}";

        Schema::create($table_full_name, function (Blueprint $table) {
            $table->integer('player_id');
            $table->smallInteger('date_id');
            $table->smallInteger('release_id')->nullable();
            $table->smallInteger('pbs');
            $table->smallInteger('leaderboards');
            $table->smallInteger('first_place_ranks');
            $table->smallInteger('dailies');
            $table->jsonb('leaderboard_types')->nullable();
            $table->jsonb('characters')->nullable();
            $table->jsonb('modes')->nullable();
            $table->jsonb('seeded_types')->nullable();
            $table->jsonb('multiplayer_types')->nullable();
            $table->jsonb('soundtracks')->nullable();
            $table->string('hash', 8)->nullable();
            $table->jsonb('details');

            $table->unique([
                'player_id',
                'release_id',
                'hash'
            ]);

            $table->index('date_id');
        });

        $constraint_prefix = "{$this->leaderboard_source->name}_{$table_short_name}";

        DB::statement("
            ALTER TABLE {$table_full_name}
                ADD CONSTRAINT {$constraint_prefix}_player_id_foreign
                    FOREIGN KEY (player_id) REFERENCES {$this->leaderboard_source->name}.players (id) ON DELETE CASCADE,
                ADD CONSTRAINT {$constraint_prefix}_release_id_foreign
                    FOREIGN KEY (release_id) REFERENCES public.releases (id) ON DELETE CASCADE,
                ADD CONSTRAINT {$constraint_prefix}_date_id_foreign
                    FOREIGN KEY (date_id) REFERENCES public.dates (id) ON DELETE CASCADE;
        ");
    }
}
