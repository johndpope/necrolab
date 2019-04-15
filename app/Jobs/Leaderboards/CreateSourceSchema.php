<?php

namespace App\Jobs\Leaderboards;

use DateTime;
use DatePeriod;
use DateInterval;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Jobs\Leaderboards\Entries\CreatePartition as CreateLeaderboardEntriesPartitionJob;
use App\Jobs\Rankings\Power\Entries\CreatePartition as CreatePowerRankingEntriesPartitionJob;
use App\Jobs\Rankings\Daily\Entries\CreatePartition as CreateDailyRankingEntriesPartitionJob;
use App\LeaderboardSources;
use App\LeaderboardEntries;
use App\PowerRankingEntries;
use App\DailyRankingEntries;

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
        $table_short_name = "leaderboards";
        
        $table_full_name = "{$this->leaderboard_source->name}.{$table_short_name}";
        
        Schema::create($table_full_name, function (Blueprint $table) {
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
        
        $constraint_prefix = "{$this->leaderboard_source->name}_{$table_short_name}";
        
        DB::statement("
            ALTER TABLE {$table_full_name}
                ADD CONSTRAINT {$constraint_prefix}_leaderboard_type_id_foreign
                    FOREIGN KEY (leaderboard_type_id) REFERENCES public.leaderboard_types (id) ON DELETE CASCADE,
                ADD CONSTRAINT {$constraint_prefix}_character_id_foreign
                    FOREIGN KEY (character_id) REFERENCES public.characters (id) ON DELETE CASCADE,
                ADD CONSTRAINT {$constraint_prefix}_release_id_foreign
                    FOREIGN KEY (release_id) REFERENCES public.releases (id) ON DELETE CASCADE,
                ADD CONSTRAINT {$constraint_prefix}_mode_id_foreign
                    FOREIGN KEY (mode_id) REFERENCES public.modes (id) ON DELETE CASCADE,
                ADD CONSTRAINT {$constraint_prefix}_seeded_type_id_foreign
                    FOREIGN KEY (seeded_type_id) REFERENCES public.seeded_types (id) ON DELETE CASCADE,
                ADD CONSTRAINT {$constraint_prefix}_multiplayer_type_id_foreign
                    FOREIGN KEY (multiplayer_type_id) REFERENCES public.multiplayer_types (id) ON DELETE CASCADE,
                ADD CONSTRAINT {$constraint_prefix}_soundtrack_id_foreign
                    FOREIGN KEY (soundtrack_id) REFERENCES public.soundtracks (id) ON DELETE CASCADE,
                ADD CONSTRAINT {$constraint_prefix}_daily_date_id_foreign
                    FOREIGN KEY (daily_date_id) REFERENCES public.dates (id) ON DELETE CASCADE;
        ");
        
        $this->createTableSequence($table_full_name);
    }
    
    /**
     * Creates the leaderboards_blacklist table.
     *
     * @return void
     */
    protected function createLeaderboardsBlacklistTable() {        
        $table_short_name = "leaderboards_blacklist";
        
        $table_full_name = "{$this->leaderboard_source->name}.{$table_short_name}";
        
        Schema::create($table_full_name, function (Blueprint $table) {
            $table->timestamp('created');
            $table->integer('leaderboard_id')->unique();
            
            $table->primary('leaderboard_id');
        });
        
        $constraint_prefix = "{$this->leaderboard_source->name}_{$table_short_name}";
        
        DB::statement("
            ALTER TABLE {$table_full_name}
                ADD CONSTRAINT {$constraint_prefix}_leaderboard_id_foreign
                    FOREIGN KEY (leaderboard_id) REFERENCES {$this->leaderboard_source->name}.leaderboards (id) ON DELETE CASCADE;
        ");
    }
    
    
    /**
     * Creates the leaderboard_snapshots table.
     *
     * @return void
     */
    protected function createLeaderboardSnapshotsTable() {        
        $table_short_name = "leaderboard_snapshots";
        
        $table_full_name = "{$this->leaderboard_source->name}.{$table_short_name}";
        
        Schema::create($table_full_name, function (Blueprint $table) {
            $table->timestamp('created');
            $table->timestamp('updated')->nullable();
            $table->integer('id');
            $table->integer('leaderboard_id');
            $table->integer('players')->nullable();
            $table->smallInteger('date_id');
            $table->jsonb('details')->nullable();
            
            $table->primary('id');
            
            $table->unique([
                'leaderboard_id',
                'date_id'
            ]);
            
            $table->index('date_id');
        });
        
        $constraint_prefix = "{$this->leaderboard_source->name}_{$table_short_name}";
        
        DB::statement("
            ALTER TABLE {$table_full_name}
                ADD CONSTRAINT {$constraint_prefix}_leaderboard_id_foreign
                    FOREIGN KEY (leaderboard_id) REFERENCES {$this->leaderboard_source->name}.leaderboards (id) ON DELETE CASCADE,
                ADD CONSTRAINT {$constraint_prefix}_date_id_foreign
                    FOREIGN KEY (date_id) REFERENCES public.dates (id) ON DELETE CASCADE;
        ");
        
        $this->createTableSequence($table_full_name);
    }
    
    /**
     * Creates the leaderboard_entry_details table.
     *
     * @return void
     */
    protected function createLeaderboardEntryDetailsTable() {        
        $table_short_name = "leaderboard_entry_details";
        
        $table_full_name = "{$this->leaderboard_source->name}.{$table_short_name}";
        
        Schema::create($table_full_name, function (Blueprint $table) {
            $table->smallInteger('id');
            $table->string('name', 255)->unique();

            
            $table->primary('id');
        });
        
        $this->createTableSequence($table_full_name);
    }
    
    /**
     * Creates the leaderboard_ranking_types table.
     *
     * @return void
     */
    protected function createLeaderboardRankingTypesTable() {        
        $table_short_name = "leaderboard_ranking_types";
        
        $table_full_name = "{$this->leaderboard_source->name}.{$table_short_name}";
        
        Schema::create($table_full_name, function (Blueprint $table) {
            $table->integer('leaderboard_id');
            $table->smallInteger('ranking_type_id');
            
            $table->primary([
                'leaderboard_id',
                'ranking_type_id'
            ]);
            
            $table->index('ranking_type_id');
        });
        
        $constraint_prefix = "{$this->leaderboard_source->name}_{$table_short_name}";
        
        DB::statement("
            ALTER TABLE {$table_full_name}
                ADD CONSTRAINT {$constraint_prefix}_leaderboard_id_foreign
                    FOREIGN KEY (leaderboard_id) REFERENCES {$this->leaderboard_source->name}.leaderboards (id) ON DELETE CASCADE,
                ADD CONSTRAINT {$constraint_prefix}_ranking_type_id_foreign
                    FOREIGN KEY (ranking_type_id) REFERENCES public.ranking_types (id) ON DELETE CASCADE;
        ");
    }
    
    /**
     * Creates the power_rankings table.
     *
     * @return void
     */
    protected function createPowerRankingsTable() {        
        $table_short_name = "power_rankings";
        
        $table_full_name = "{$this->leaderboard_source->name}.{$table_short_name}";
        
        Schema::create($table_full_name, function (Blueprint $table) {
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
            $table->jsonb('categories')->nullable();
            $table->jsonb('characters')->nullable();
            
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
        
        $constraint_prefix = "{$this->leaderboard_source->name}_{$table_short_name}";
        
        DB::statement("
            ALTER TABLE {$table_full_name}
                ADD CONSTRAINT {$constraint_prefix}_release_id_foreign
                    FOREIGN KEY (release_id) REFERENCES public.releases (id) ON DELETE CASCADE,
                ADD CONSTRAINT {$constraint_prefix}_mode_id_foreign
                    FOREIGN KEY (mode_id) REFERENCES public.modes (id) ON DELETE CASCADE,
                ADD CONSTRAINT {$constraint_prefix}_seeded_type_id_foreign
                    FOREIGN KEY (seeded_type_id) REFERENCES public.seeded_types (id) ON DELETE CASCADE,
                ADD CONSTRAINT {$constraint_prefix}_multiplayer_type_id_foreign
                    FOREIGN KEY (multiplayer_type_id) REFERENCES public.multiplayer_types (id) ON DELETE CASCADE,
                ADD CONSTRAINT {$constraint_prefix}_soundtrack_id_foreign
                    FOREIGN KEY (soundtrack_id) REFERENCES public.soundtracks (id) ON DELETE CASCADE,
                ADD CONSTRAINT {$constraint_prefix}_date_id_foreign
                    FOREIGN KEY (date_id) REFERENCES public.dates (id) ON DELETE CASCADE;
        ");
        
        $this->createTableSequence($table_full_name);
    }
    
    /**
     * Creates the daily_rankings table.
     *
     * @return void
     */
    protected function createDailyRankingsTable() {        
        $table_short_name = "daily_rankings";
        
        $table_full_name = "{$this->leaderboard_source->name}.{$table_short_name}";
        
        Schema::create($table_full_name, function (Blueprint $table) {
            $table->timestamp('created');
            $table->timestamp('updated')->nullable();
            $table->bigInteger('dailies')->nullable();
            $table->bigInteger('wins')->nullable();
            $table->integer('id');
            $table->integer('players')->nullable();
            $table->smallInteger('character_id');
            $table->smallInteger('release_id');
            $table->smallInteger('mode_id');
            $table->smallInteger('multiplayer_type_id');
            $table->smallInteger('soundtrack_id');
            $table->smallInteger('daily_ranking_day_type_id');
            $table->smallInteger('date_id');
            $table->jsonb('details')->nullable();
            
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
        
        $constraint_prefix = "{$this->leaderboard_source->name}_{$table_short_name}";
        
        DB::statement("
            ALTER TABLE {$table_full_name}
                ADD CONSTRAINT {$constraint_prefix}_character_id_foreign
                    FOREIGN KEY (character_id) REFERENCES public.characters (id) ON DELETE CASCADE,
                ADD CONSTRAINT {$constraint_prefix}_release_id_foreign
                    FOREIGN KEY (release_id) REFERENCES public.releases (id) ON DELETE CASCADE,
                ADD CONSTRAINT {$constraint_prefix}_mode_id_foreign
                    FOREIGN KEY (mode_id) REFERENCES public.modes (id) ON DELETE CASCADE,
                ADD CONSTRAINT {$constraint_prefix}_multiplayer_type_id_foreign
                    FOREIGN KEY (multiplayer_type_id) REFERENCES public.multiplayer_types (id) ON DELETE CASCADE,
                ADD CONSTRAINT {$constraint_prefix}_soundtrack_id_foreign
                    FOREIGN KEY (soundtrack_id) REFERENCES public.soundtracks (id) ON DELETE CASCADE,
                ADD CONSTRAINT {$constraint_prefix}_daily_ranking_day_type_id_foreign
                    FOREIGN KEY (daily_ranking_day_type_id) REFERENCES public.daily_ranking_day_types (id) ON DELETE CASCADE,
                ADD CONSTRAINT {$constraint_prefix}_date_id_foreign
                    FOREIGN KEY (date_id) REFERENCES public.dates (id) ON DELETE CASCADE;
        ");
        
        $this->createTableSequence($table_full_name);
    }
    
    /**
     * Creates the players table.
     *
     * @return void
     */
    protected function createPlayersTable() {
        $table_short_name = "players";
        
        $table_full_name = "{$this->leaderboard_source->name}.{$table_short_name}";
        
        Schema::create($table_full_name, function (Blueprint $table) {
            $table->timestamp('created');
            $table->timestamp('updated');
            $table->integer('id');
            $table->string('external_id', 255)->unique();
            $table->string('username', 255)->nullable();
            $table->text('profile_url')->nullable();
            $table->text('avatar_url')->nullable();
            
            $table->primary('id');
        });
        
        DB::statement("
            ALTER TABLE {$table_full_name}
            ADD COLUMN username_search_index tsvector
        ");
        
        DB::statement("
            CREATE INDEX idx_players_username_search_index 
            ON {$table_full_name}
            USING GIN (username_search_index);
        ");
        
        $this->createTableSequence($table_full_name);
    }
    
    /**
     * Creates the daily_rankings table.
     *
     * @return void
     */
    protected function createPlayerPbsTable() {        
        $table_short_name = "player_pbs";
        
        $table_full_name = "{$this->leaderboard_source->name}.{$table_short_name}";
        
        Schema::create($table_full_name, function (Blueprint $table) {
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
            $table->jsonb('details');
            
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
        
        $constraint_prefix = "{$this->leaderboard_source->name}_{$table_short_name}";
        
        DB::statement("
            ALTER TABLE {$table_full_name}
                ADD CONSTRAINT {$constraint_prefix}_player_id_foreign
                    FOREIGN KEY (player_id) REFERENCES {$this->leaderboard_source->name}.players (id) ON DELETE CASCADE,
                ADD CONSTRAINT {$constraint_prefix}_leaderboard_id_foreign
                    FOREIGN KEY (leaderboard_id) REFERENCES {$this->leaderboard_source->name}.leaderboards (id) ON DELETE CASCADE,
                ADD CONSTRAINT {$constraint_prefix}_first_leaderboard_snapshot_id_foreign
                    FOREIGN KEY (first_leaderboard_snapshot_id) REFERENCES {$this->leaderboard_source->name}.leaderboard_snapshots (id) ON DELETE CASCADE,
                ADD CONSTRAINT {$constraint_prefix}_leaderboard_entry_details_id_foreign
                    FOREIGN KEY (leaderboard_entry_details_id) REFERENCES {$this->leaderboard_source->name}.leaderboard_entry_details (id) ON DELETE CASCADE;
        ");
        
        $this->createTableSequence($table_full_name);
    }
    
    /**
     * Creates the players_blacklist table.
     *
     * @return void
     */
    protected function createPlayersBlacklistTable() {        
        $table_short_name = "players_blacklist";
        
        $table_full_name = "{$this->leaderboard_source->name}.{$table_short_name}";
        
        Schema::create($table_full_name, function (Blueprint $table) {
            $table->timestamp('created');
            $table->integer('player_id')->unique();
            
            $table->primary('player_id');
        });
        
        $constraint_prefix = "{$this->leaderboard_source->name}_{$table_short_name}";
        
        DB::statement("
            ALTER TABLE {$table_full_name}
                ADD CONSTRAINT {$constraint_prefix}_player_id_foreign
                    FOREIGN KEY (player_id) REFERENCES {$this->leaderboard_source->name}.players (id) ON DELETE CASCADE;
        ");
    }
    
    /**
     * Creates the achievements table.
     *
     * @return void
     */
    protected function createAchievementsTable() {        
        $table_short_name = "achievements";
        
        $table_full_name = "{$this->leaderboard_source->name}.{$table_short_name}";
        
        Schema::create($table_full_name, function (Blueprint $table) {
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
        $table_short_name = "player_achievements";
        
        $table_full_name = "{$this->leaderboard_source->name}.{$table_short_name}";
        
        Schema::create($table_full_name, function (Blueprint $table) {
            $table->timestamp('achieved');
            $table->integer('player_id');
            $table->smallInteger('achievement_id');
            
            $table->primary([
                'player_id',
                'achievement_id'
            ]);
            
            $table->index('achievement_id');
        });
        
        $constraint_prefix = "{$this->leaderboard_source->name}_{$table_short_name}";
        
        DB::statement("
            ALTER TABLE {$table_full_name}
                ADD CONSTRAINT {$constraint_prefix}_player_id_foreign
                    FOREIGN KEY (player_id) REFERENCES {$this->leaderboard_source->name}.players (id) ON DELETE CASCADE,
                ADD CONSTRAINT {$constraint_prefix}_achievement_id_foreign
                    FOREIGN KEY (achievement_id) REFERENCES {$this->leaderboard_source->name}.achievements (id) ON DELETE CASCADE;
        ");
    }
    
    /**
     * Creates the replay_versions table.
     *
     * @return void
     */
    protected function createReplayVersionsTable() {
        $table_short_name = "replay_versions";
        
        $table_full_name = "{$this->leaderboard_source->name}.{$table_short_name}";
        
        Schema::create($table_full_name, function (Blueprint $table) {
            $table->smallInteger('id');
            $table->string('name', 255)->unique();

            
            $table->primary('id');
        });
        
        $this->createTableSequence($table_full_name);
    }
    
    /**
     * Creates the run_results table.
     *
     * @return void
     */
    protected function createRunResultsTable() {
        $table_short_name = "run_results";
        
        $table_full_name = "{$this->leaderboard_source->name}.{$table_short_name}";
        
        Schema::create($table_full_name, function (Blueprint $table) {
            $table->smallInteger('id');
            $table->smallInteger('is_win');
            $table->string('name', 255)->unique();

            
            $table->primary('id');
        });
        
        $this->createTableSequence($table_full_name);
    }
    
    /**
     * Creates the seeds table.
     *
     * @return void
     */
    protected function createSeedsTable() {
        $table_short_name = "seeds";
        
        $table_full_name = "{$this->leaderboard_source->name}.{$table_short_name}";
        
        Schema::create($table_full_name, function (Blueprint $table) {
            $table->bigInteger('id');
            $table->string('name', 255)->unique();

            
            $table->primary('id');
        });
        
        $this->createTableSequence($table_full_name);
    }
    
    /**
     * Creates the replays table.
     *
     * @return void
     */
    protected function createReplaysTable() {
        $table_short_name = "replays";
        
        $table_full_name = "{$this->leaderboard_source->name}.{$table_short_name}";
        
        Schema::create($table_full_name, function (Blueprint $table) {
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
            
            $table->index([
                'downloaded',
                'invalid'
            ]);
            
            $table->index([
                'uploaded_to_s3',
                'downloaded',
                'invalid'
            ]);
            
            $table->index('invalid');
        });
        
        $constraint_prefix = "{$this->leaderboard_source->name}_{$table_short_name}";
        
        DB::statement("
            ALTER TABLE {$table_full_name}
                ADD CONSTRAINT {$constraint_prefix}_seed_id_foreign
                    FOREIGN KEY (seed_id) REFERENCES {$this->leaderboard_source->name}.seeds (id) ON DELETE CASCADE,
                ADD CONSTRAINT {$constraint_prefix}_player_pb_id_foreign
                    FOREIGN KEY (player_pb_id) REFERENCES {$this->leaderboard_source->name}.player_pbs (id) ON DELETE CASCADE,
                ADD CONSTRAINT {$constraint_prefix}_player_id_foreign
                    FOREIGN KEY (player_id) REFERENCES {$this->leaderboard_source->name}.players (id) ON DELETE CASCADE,
                ADD CONSTRAINT {$constraint_prefix}_run_result_id_foreign
                    FOREIGN KEY (run_result_id) REFERENCES {$this->leaderboard_source->name}.run_results (id) ON DELETE CASCADE,
                ADD CONSTRAINT {$constraint_prefix}_replay_version_id_foreign
                    FOREIGN KEY (replay_version_id) REFERENCES {$this->leaderboard_source->name}.replay_versions (id) ON DELETE CASCADE;
        ");
    }
    
    /**
     * Creates the entry_indexes table.
     *
     * @return void
     */
    protected function createEntryIndexesTable() {
        $table_short_name = "entry_indexes";
        
        $table_full_name = "{$this->leaderboard_source->name}.{$table_short_name}";
        
        Schema::create($table_full_name, function (Blueprint $table) {
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
     * Creates the table that links necrolab users to their respective source player.
     *
     * @return void
     */
    protected function createPlayersLinkTable() {
        $table_name = "user_{$this->leaderboard_source->name}_player";
    
        Schema::create($table_name, function (Blueprint $table) {
            $table->timestamp('created');
            $table->integer('user_id');
            $table->integer('player_id');

            $table->primary('user_id');
            
            $table->index('player_id');
        });
        
        DB::statement("
            ALTER TABLE {$table_name}
                ADD CONSTRAINT {$table_name}_user_id_foreign
                    FOREIGN KEY (user_id) REFERENCES public.users (id) ON DELETE CASCADE,
                ADD CONSTRAINT {$table_name}_player_id_foreign
                    FOREIGN KEY (player_id) REFERENCES {$this->leaderboard_source->name}.players (id) ON DELETE CASCADE;
        ");
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
        
        $this->createPlayersLinkTable();
        
        $start_date = new DateTime($this->leaderboard_source->start_date);
        $start_date->modify('first day of this month');
        
        $end_date = new DateTime($this->leaderboard_source->end_date);
        $end_date->modify('first day of next month');
        
        $date_period = new DatePeriod(
            $start_date,
            new DateInterval('P1M'),
            $end_date
        );
        
        foreach($date_period as $date) {
            CreateLeaderboardEntriesPartitionJob::dispatch(
                $this->leaderboard_source,
                $date
            )->onConnection('sync');
            
            CreatePowerRankingEntriesPartitionJob::dispatch(
                $this->leaderboard_source,
                $date
            )->onConnection('sync');
            
            CreateDailyRankingEntriesPartitionJob::dispatch(
                $this->leaderboard_source,
                $date
            )->onConnection('sync');
        }
    }
}
