<?php

namespace App\Jobs\Leaderboards;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\LeaderboardSources;

class CreateSourceSchema implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;
    
    /**
     * The leaderboard_source record that this job is working in the context of.
     *
     * @var LeaderboardSources
     */
    protected $leaderboard_source;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(LeaderboardSources $leaderboard_source) {
        $this->leaderboard_source = $leaderboard_source;
    }
    
    /**
     * Creates the sequence for the specified table.
     *
     * @param string $table_name The name of the table to create a sequence for.
     * @return void
     */
    protected function createTableSequence(string $table_name) {
        DB::statement("
            CREATE SEQUENCE {$table_name}_seq
                INCREMENT 1
                MINVALUE 1
                MAXVALUE 9223372036854775807
                START 1
                CACHE 1;
        ");
    }
    
    /**
     * Creates the leaderboards table.
     *
     * @return void
     */
    protected function createLeaderboardsTable() {        
        $table_name = "{$this->leaderboard_source->name}.leaderboards";
        
        Schema::create($table_name, function (Blueprint $table) {
            $table->integer('id');
            $table->smallInteger('leaderboard_type_id');
            $table->smallInteger('character_id');
            $table->smallInteger('release_id');
            $table->smallInteger('mode_id');
            $table->smallInteger('seeded_type_id');
            $table->smallInteger('multiplayer_type_id');
            $table->smallInteger('soundtrack_id');
            $table->smallInteger('daily_date_id')->nullable();
            $table->string('external_id', 255)->unique();
            $table->string('name', 255);
            $table->string('display_name', 255)->nullable();
            $table->string('url', 255)->nullable();
            
            $table->primary('id');
            
            $table->index('leaderboard_type_id');
            $table->index('character_id');
            $table->index('release_id');
            $table->index('mode_id');
            $table->index('seeded_type_id');
            $table->index('multiplayer_type_id');
            $table->index('soundtrack_id');
            $table->index('daily_date_id');
            
            // The unique index for this record
            $table->index([
                'leaderboard_type_id',
                'character_id',
                'release_id',
                'mode_id',
                'seeded_type_id',
                'multiplayer_type_id',
                'soundtrack_id',
                'daily_date_id'
            ]);
            
            
            /* 
                The following foreign key declarations don't work because Laravel will double quote both the schema and table name
                ("steam"."leaderboards"). This causes Postgres to throw a SQL syntax error.
                
                This will be kept in until this has been fixed in the main framework.
            */
            
            /*            
            
            $table->foreign('leaderboard_type_id')
                ->references('public.leaderboard_types')
                ->on('leaderboard_type_id')
                ->onDelete('cascade');
            
            
            $table->foreign('character_id')
                ->references('public.characters')
                ->on('character_id')
                ->onDelete('cascade');
            
            
            $table->foreign('release_id')
                ->references('public.releases')
                ->on('release_id')
                ->onDelete('cascade');
            
            
            $table->foreign('mode_id')
                ->references('public.modes')
                ->on('mode_id')
                ->onDelete('cascade');
            
            
            $table->foreign('seeded_type_id')
                ->references('public.seeded_types')
                ->on('id')
                ->onDelete('cascade');
            
            
            $table->foreign('multiplayer_type_id')
                ->references('public.multiplayer_types')
                ->on('id')
                ->onDelete('cascade');
            
            
            $table->foreign('soundtrack_id')
                ->references('public.soundtracks')
                ->on('id')
                ->onDelete('cascade');
            */
        });
        
        DB::statement("
            ALTER TABLE {$table_name}
                ADD CONSTRAINT {$this->leaderboard_source->name}_leaderboards_leaderboard_type_id_foreign
                    FOREIGN KEY (leaderboard_type_id) REFERENCES public.leaderboard_types (id) ON DELETE CASCADE,
                ADD CONSTRAINT {$this->leaderboard_source->name}_leaderboards_character_id_foreign
                    FOREIGN KEY (character_id) REFERENCES public.characters (id) ON DELETE CASCADE,
                ADD CONSTRAINT {$this->leaderboard_source->name}_leaderboards_release_id_foreign
                    FOREIGN KEY (release_id) REFERENCES public.releases (id) ON DELETE CASCADE,
                ADD CONSTRAINT {$this->leaderboard_source->name}_leaderboards_mode_id_foreign
                    FOREIGN KEY (mode_id) REFERENCES public.modes (id) ON DELETE CASCADE,
                ADD CONSTRAINT {$this->leaderboard_source->name}_leaderboards_seeded_type_id_foreign
                    FOREIGN KEY (seeded_type_id) REFERENCES public.seeded_types (id) ON DELETE CASCADE,
                ADD CONSTRAINT {$this->leaderboard_source->name}_leaderboards_multiplayer_type_id_foreign
                    FOREIGN KEY (multiplayer_type_id) REFERENCES public.multiplayer_types (id) ON DELETE CASCADE,
                ADD CONSTRAINT {$this->leaderboard_source->name}_leaderboards_soundtrack_id_foreign
                    FOREIGN KEY (soundtrack_id) REFERENCES public.soundtracks (id) ON DELETE CASCADE,
                ADD CONSTRAINT {$this->leaderboard_source->name}_leaderboards_daily_date_id_foreign
                    FOREIGN KEY (daily_date_id) REFERENCES public.dates (id) ON DELETE CASCADE;
        ");
        
        $this->createTableSequence($table_name);
    }
    
    /**
     * Creates the leaderboards_blacklist table.
     *
     * @return void
     */
    protected function createLeaderboardsBlacklistTable() {
        $table_name = "{$this->leaderboard_source->name}.leaderboards_blacklist";
        
        Schema::create($table_name, function (Blueprint $table) {
            $table->timestamp('created');
            $table->integer('leaderboard_id');
            
            $table->primary('leaderboard_id');
        });
        
        DB::statement("
            ALTER TABLE {$table_name}
                ADD CONSTRAINT {$this->leaderboard_source->name}_leaderboards_blacklist_leaderboard_id_foreign
                    FOREIGN KEY (leaderboard_id) REFERENCES {$this->leaderboard_source->name}.leaderboards (id) ON DELETE CASCADE;
        ");
    }
    
    
    /**
     * Creates the leaderboard_snapshots table.
     *
     * @return void
     */
    protected function createLeaderboardSnapshotsTable() {
        $table_name = "{$this->leaderboard_source->name}.leaderboard_snapshots";
        
        Schema::create($table_name, function (Blueprint $table) {
            $table->timestamp('created');
            $table->timestamp('updated')->nullable();
            $table->integer('id');
            $table->integer('leaderboard_id');
            $table->integer('players')->nullable();
            $table->smallInteger('date_id');
            $table->binary('details')->nullable();
            
            $table->primary('id');
            
            $table->unique([
                'leaderboard_id',
                'date_id'
            ]);
            
            $table->index('date_id');
        });
        
        DB::statement("
            ALTER TABLE {$table_name}
                ADD CONSTRAINT {$this->leaderboard_source->name}_leaderboard_snapshots_leaderboard_id_foreign
                    FOREIGN KEY (leaderboard_id) REFERENCES {$this->leaderboard_source->name}.leaderboards (id) ON DELETE CASCADE,
                ADD CONSTRAINT {$this->leaderboard_source->name}_leaderboard_snapshots_date_id_foreign
                    FOREIGN KEY (date_id) REFERENCES public.dates (id) ON DELETE CASCADE;
        ");
        
        $this->createTableSequence($table_name);
    }
    
    /**
     * Creates the leaderboard_entry_details table.
     *
     * @return void
     */
    protected function createLeaderboardEntryDetailsTable() {
        $table_name = "{$this->leaderboard_source->name}.leaderboard_entry_details";
        
        Schema::create($table_name, function (Blueprint $table) {
            $table->smallInteger('id');
            $table->string('name', 255)->unique();

            
            $table->primary('id');
        });
        
        $this->createTableSequence($table_name);
    }
    
    /**
     * Creates the leaderboard_ranking_types table.
     *
     * @return void
     */
    protected function createLeaderboardRankingTypesTable() {
        $table_name = "{$this->leaderboard_source->name}.leaderboard_ranking_types";
        
        Schema::create($table_name, function (Blueprint $table) {
            $table->integer('leaderboard_id');
            $table->smallInteger('ranking_type_id');
            
            $table->primary([
                'leaderboard_id',
                'ranking_type_id'
            ]);
            
            $table->index('ranking_type_id');
        });
        
        DB::statement("
            ALTER TABLE {$table_name}
                ADD CONSTRAINT {$this->leaderboard_source->name}_leaderboard_ranking_types_leaderboard_id_foreign
                    FOREIGN KEY (leaderboard_id) REFERENCES {$this->leaderboard_source->name}.leaderboards (id) ON DELETE CASCADE,
                ADD CONSTRAINT {$this->leaderboard_source->name}_leaderboard_ranking_type_id_foreign
                    FOREIGN KEY (ranking_type_id) REFERENCES public.ranking_types (id) ON DELETE CASCADE;
        ");
    }
    
    /**
     * Creates the power_rankings table.
     *
     * @return void
     */
    protected function createPowerRankingsTable() {
        $table_name = "{$this->leaderboard_source->name}.power_rankings";
        
        Schema::create($table_name, function (Blueprint $table) {
            $table->timestamp('created');
            $table->timestamp('updated')->nullable();
            $table->integer('id');
            $table->integer('players')->nullable();
            $table->smallInteger('release_id');
            $table->smallInteger('mode_id');
            $table->smallInteger('seeded_type_id');
            $table->smallInteger('multiplayer_type_id');
            $table->smallInteger('soundtrack_id');
            $table->smallInteger('date_id');
            $table->binary('categories')->nullable();
            $table->binary('characters')->nullable();
            
            $table->primary('id');
            
            $table->unique([
                'release_id',
                'mode_id',
                'seeded_type_id',
                'multiplayer_type_id',
                'soundtrack_id',
                'date_id'
            ]);
            
            $table->index('mode_id');
            $table->index('seeded_type_id');
            $table->index('multiplayer_type_id');
            $table->index('soundtrack_id');
            $table->index('date_id');
        });
        
        DB::statement("
            ALTER TABLE {$table_name}
                ADD CONSTRAINT {$this->leaderboard_source->name}_power_rankings_release_id_foreign
                    FOREIGN KEY (release_id) REFERENCES public.releases (id) ON DELETE CASCADE,
                ADD CONSTRAINT {$this->leaderboard_source->name}_power_rankings_mode_id_foreign
                    FOREIGN KEY (mode_id) REFERENCES public.modes (id) ON DELETE CASCADE,
                ADD CONSTRAINT {$this->leaderboard_source->name}_power_rankings_seeded_type_id_foreign
                    FOREIGN KEY (seeded_type_id) REFERENCES public.seeded_types (id) ON DELETE CASCADE,
                ADD CONSTRAINT {$this->leaderboard_source->name}_power_rankings_multiplayer_type_id_foreign
                    FOREIGN KEY (multiplayer_type_id) REFERENCES public.multiplayer_types (id) ON DELETE CASCADE,
                ADD CONSTRAINT {$this->leaderboard_source->name}_power_rankings_soundtrack_id_foreign
                    FOREIGN KEY (soundtrack_id) REFERENCES public.soundtracks (id) ON DELETE CASCADE,
                ADD CONSTRAINT {$this->leaderboard_source->name}_power_rankings_date_id_foreign
                    FOREIGN KEY (date_id) REFERENCES public.dates (id) ON DELETE CASCADE;
        ");
        
        $this->createTableSequence($table_name);
    }
    
    /**
     * Creates the daily_rankings table.
     *
     * @return void
     */
    protected function createDailyRankingsTable() {
        $table_name = "{$this->leaderboard_source->name}.daily_rankings";
        
        Schema::create($table_name, function (Blueprint $table) {
            $table->timestamp('created');
            $table->timestamp('updated')->nullable();
            $table->integer('id');
            $table->integer('players')->nullable();
            $table->smallInteger('character_id');
            $table->smallInteger('release_id');
            $table->smallInteger('mode_id');
            $table->smallInteger('multiplayer_type_id');
            $table->smallInteger('soundtrack_id');
            $table->smallInteger('daily_ranking_day_type_id');
            $table->smallInteger('date_id');
            $table->smallInteger('dailies');
            $table->smallInteger('wins');
            $table->binary('totals')->nullable();
            
            $table->primary('id');
            
            $table->unique([
                'character_id',
                'release_id',
                'mode_id',
                'multiplayer_type_id',
                'soundtrack_id',
                'daily_ranking_day_type_id',
                'date_id'
            ]);
            
            $table->index('release_id');
            $table->index('mode_id');
            $table->index('multiplayer_type_id');
            $table->index('soundtrack_id');
            $table->index('daily_ranking_day_type_id');
            $table->index('date_id');
        });
        
        DB::statement("
            ALTER TABLE {$table_name}
                ADD CONSTRAINT {$this->leaderboard_source->name}_daily_rankings_character_id_foreign
                    FOREIGN KEY (character_id) REFERENCES public.characters (id) ON DELETE CASCADE,
                ADD CONSTRAINT {$this->leaderboard_source->name}_daily_rankings_release_id_foreign
                    FOREIGN KEY (release_id) REFERENCES public.releases (id) ON DELETE CASCADE,
                ADD CONSTRAINT {$this->leaderboard_source->name}_daily_rankings_mode_id_foreign
                    FOREIGN KEY (mode_id) REFERENCES public.modes (id) ON DELETE CASCADE,
                ADD CONSTRAINT {$this->leaderboard_source->name}_daily_rankings_multiplayer_type_id_foreign
                    FOREIGN KEY (multiplayer_type_id) REFERENCES public.multiplayer_types (id) ON DELETE CASCADE,
                ADD CONSTRAINT {$this->leaderboard_source->name}_daily_rankings_soundtrack_id_foreign
                    FOREIGN KEY (soundtrack_id) REFERENCES public.soundtracks (id) ON DELETE CASCADE,
                ADD CONSTRAINT {$this->leaderboard_source->name}_daily_rankings_daily_ranking_day_type_id_foreign
                    FOREIGN KEY (daily_ranking_day_type_id) REFERENCES public.daily_ranking_day_types (id) ON DELETE CASCADE,
                ADD CONSTRAINT {$this->leaderboard_source->name}_daily_rankings_date_id_foreign
                    FOREIGN KEY (date_id) REFERENCES public.dates (id) ON DELETE CASCADE;
        ");
        
        $this->createTableSequence($table_name);
    }
    
    /**
     * Creates the players table.
     *
     * @return void
     */
    protected function createPlayersTable() {
        $table_name = "{$this->leaderboard_source->name}.players";
        
        Schema::create($table_name, function (Blueprint $table) {
            $table->timestamp('created');
            $table->timestamp('updated');
            $table->integer('id');
            $table->string('external_id', 255)->unique();
            $table->string('username', 255);
            $table->text('profile_url', 255);
            $table->text('avatar_url', 255);
            
            $table->primary('id');
        });
        
        DB::statement("
            ALTER TABLE {$table_name}
            ADD COLUMN username_search_index tsvector
        ");
        
        DB::statement("
            CREATE INDEX idx_players_username_search_index 
            ON {$table_name}
            USING GIN (username_search_index);
        ");
        
        $this->createTableSequence($table_name);
    }
    
    /**
     * Creates the daily_rankings table.
     *
     * @return void
     */
    protected function createPlayerPbsTable() {
        $table_name = "{$this->leaderboard_source->name}.player_pbs";
        
        Schema::create($table_name, function (Blueprint $table) {
            $table->integer('id');
            $table->integer('player_id');
            $table->integer('leaderboard_id');
            $table->integer('first_leaderboard_snapshot_id');
            $table->integer('first_rank');
            $table->smallInteger('leaderboard_entry_details_id');
            $table->smallInteger('zone');
            $table->smallInteger('level');
            $table->smallInteger('is_win');
            $table->string('raw_score', 255);
            $table->binary('details');
            
            $table->primary('id');
            
            $table->unique([
                'leaderboard_id',
                'player_id',
                'raw_score'
            ]);
            
            $table->index('player_id');
            $table->index('first_leaderboard_snapshot_id');
            $table->index('leaderboard_entry_details_id');
        });
        
        DB::statement("
            ALTER TABLE {$table_name}
                ADD CONSTRAINT {$this->leaderboard_source->name}_player_pbs_player_id_foreign
                    FOREIGN KEY (player_id) REFERENCES {$this->leaderboard_source->name}.players (id) ON DELETE CASCADE,
                ADD CONSTRAINT {$this->leaderboard_source->name}_player_pbs_leaderboard_id_foreign
                    FOREIGN KEY (leaderboard_id) REFERENCES {$this->leaderboard_source->name}.leaderboards (id) ON DELETE CASCADE,
                ADD CONSTRAINT {$this->leaderboard_source->name}_player_pbs_first_leaderboard_snapshot_id_foreign
                    FOREIGN KEY (first_leaderboard_snapshot_id) REFERENCES {$this->leaderboard_source->name}.leaderboard_snapshots (id) ON DELETE CASCADE,
                ADD CONSTRAINT {$this->leaderboard_source->name}_player_pbs_leaderboard_entry_details_id_foreign
                    FOREIGN KEY (leaderboard_entry_details_id) REFERENCES {$this->leaderboard_source->name}.leaderboard_entry_details (id) ON DELETE CASCADE;
        ");
        
        $this->createTableSequence($table_name);
    }
    
    /**
     * Creates the players_blacklist table.
     *
     * @return void
     */
    protected function createPlayersBlacklistTable() {
        $table_name = "{$this->leaderboard_source->name}.players_blacklist";
        
        Schema::create($table_name, function (Blueprint $table) {
            $table->timestamp('created');
            $table->integer('player_id');
            
            $table->primary('player_id');
        });
        
        DB::statement("
            ALTER TABLE {$table_name}
                ADD CONSTRAINT {$this->leaderboard_source->name}_players_blacklist_player_id_foreign
                    FOREIGN KEY (player_id) REFERENCES {$this->leaderboard_source->name}.players (id) ON DELETE CASCADE;
        ");
    }
    
    /**
     * Creates the achievements table.
     *
     * @return void
     */
    protected function createAchievementsTable() {
        $table_name = "{$this->leaderboard_source->name}.achievements";
        
        Schema::create($table_name, function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name', 255);
            $table->string('display_name', 255);
            $table->text('description');
            $table->text('icon_url');
            $table->text('icon_gray_url');
        });
    }
    
    /**
     * Creates the player_achievements table.
     *
     * @return void
     */
    protected function createPlayerAchievementsTable() {
        $table_name = "{$this->leaderboard_source->name}.player_achievements";
        
        Schema::create($table_name, function (Blueprint $table) {
            $table->timestamp('achieved');
            $table->integer('player_id');
            $table->smallInteger('achievement_id');
            
            $table->primary([
                'player_id',
                'achievement_id'
            ]);
            
            $table->index('achievement_id');
        });
        
        DB::statement("
            ALTER TABLE {$table_name}
                ADD CONSTRAINT {$this->leaderboard_source->name}_player_achievements_player_id_foreign
                    FOREIGN KEY (player_id) REFERENCES {$this->leaderboard_source->name}.players (id) ON DELETE CASCADE,
                ADD CONSTRAINT {$this->leaderboard_source->name}_player_achievements_achievement_id_foreign
                    FOREIGN KEY (achievement_id) REFERENCES {$this->leaderboard_source->name}.achievements (id) ON DELETE CASCADE;
        ");
    }
    
    /**
     * Creates the replay_versions table.
     *
     * @return void
     */
    protected function createReplayVersionsTable() {
        $table_name = "{$this->leaderboard_source->name}.replay_versions";
        
        Schema::create($table_name, function (Blueprint $table) {
            $table->smallInteger('id');
            $table->string('name', 255)->unique();

            
            $table->primary('id');
        });
        
        $this->createTableSequence($table_name);
    }
    
    /**
     * Creates the run_results table.
     *
     * @return void
     */
    protected function createRunResultsTable() {
        $table_name = "{$this->leaderboard_source->name}.run_results";
        
        Schema::create($table_name, function (Blueprint $table) {
            $table->smallInteger('id');
            $table->smallInteger('is_win');
            $table->string('name', 255)->unique();

            
            $table->primary('id');
        });
        
        $this->createTableSequence($table_name);
    }
    
    /**
     * Creates the seeds table.
     *
     * @return void
     */
    protected function createSeedsTable() {
        $table_name = "{$this->leaderboard_source->name}.seeds";
        
        Schema::create($table_name, function (Blueprint $table) {
            $table->bigInteger('id');
            $table->string('name', 255)->unique();

            
            $table->primary('id');
        });
        
        $this->createTableSequence($table_name);
    }
    
    /**
     * Creates the replays table.
     *
     * @return void
     */
    protected function createReplaysTable() {
        $table_name = "{$this->leaderboard_source->name}.replays";
        
        Schema::create($table_name, function (Blueprint $table) {
            $table->bigInteger('seed_id')->nullable();
            $table->integer('player_pb_id');
            $table->integer('player_id');
            $table->smallInteger('run_result_id')->nullable();
            $table->smallInteger('replay_version_id')->nullable();
            $table->smallInteger('downloaded')->default(0);
            $table->smallInteger('invalid')->default(0);
            $table->smallInteger('uploaded_to_s3')->default(0);
            $table->string('external_id', 255);
            
            $table->primary('player_pb_id');
            
            $table->index('seed_id');
            $table->index('player_id');
            $table->index('run_result_id');
            $table->index('replay_version_id');
            
            $table->index('downloaded');
            
            $table->index([
                'downloaded',
                'invalid'
            ]);
            
            $table->index('invalid');
            $table->index('uploaded_to_s3');
        });
        
        DB::statement("
            ALTER TABLE {$table_name}
                ADD CONSTRAINT {$this->leaderboard_source->name}_replays_seed_id_foreign
                    FOREIGN KEY (seed_id) REFERENCES {$this->leaderboard_source->name}.seeds (id) ON DELETE CASCADE,
                ADD CONSTRAINT {$this->leaderboard_source->name}_replays_player_pb_id_foreign
                    FOREIGN KEY (player_pb_id) REFERENCES {$this->leaderboard_source->name}.player_pbs (id) ON DELETE CASCADE,
                ADD CONSTRAINT {$this->leaderboard_source->name}_replays_player_id_foreign
                    FOREIGN KEY (player_id) REFERENCES {$this->leaderboard_source->name}.players (id) ON DELETE CASCADE,
                ADD CONSTRAINT {$this->leaderboard_source->name}_replays_run_result_id_foreign
                    FOREIGN KEY (run_result_id) REFERENCES {$this->leaderboard_source->name}.run_results (id) ON DELETE CASCADE,
                ADD CONSTRAINT {$this->leaderboard_source->name}_replays_replay_version_id_foreign
                    FOREIGN KEY (replay_version_id) REFERENCES {$this->leaderboard_source->name}.replay_versions (id) ON DELETE CASCADE;
        ");
    }
    
    /**
     * Creates the entry_indexes table.
     *
     * @return void
     */
    protected function createEntryIndexesTable() {
        $table_name = "{$this->leaderboard_source->name}.entry_indexes";
        
        Schema::create($table_name, function (Blueprint $table) {
            $table->string('name', 255);
            $table->string('sub_name', 255);
            $table->binary('data');

            $table->primary([
                'name',
                'sub_name'
            ]);
        });
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        DB::statement("
            CREATE SCHEMA {$this->leaderboard_source->name};
        ");
        
        $this->createLeaderboardsTable();
        
        $this->createLeaderboardsBlacklistTable();
        
        $this->createLeaderboardSnapshotsTable();
        
        $this->createLeaderboardEntryDetailsTable();
        
        $this->createLeaderboardRankingTypesTable();
        
        $this->createPowerRankingsTable();
        
        $this->createDailyRankingsTable();
        
        $this->createPlayersTable();
        
        $this->createPlayerPbsTable();
        
        $this->createPlayersBlacklistTable();
        
        $this->createAchievementsTable();
        
        $this->createPlayerAchievementsTable();
        
        $this->createSeedsTable();
        
        $this->createReplayVersionsTable();
        
        $this->createRunResultsTable();
        
        $this->createReplaysTable();
        
        $this->createEntryIndexesTable();
    }
}
