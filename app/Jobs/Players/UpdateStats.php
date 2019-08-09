<?php

namespace App\Jobs\Players;

use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use App\Jobs\Traits\WorksWithinDatabaseTransaction;
use App\Components\PostgresCursor;
use App\Components\Redis\DatabaseSelector;
use App\Components\Redis\Transaction\Pipeline as PipelineTransaction;
use App\Components\RecordQueue;
use App\Components\CallbackHandler;
use App\Components\CacheNames\Players as CacheNames;
use App\LeaderboardSources;
use App\LeaderboardEntries;
use App\Dates;
use App\PlayerPbs;
use App\PlayerStats;
use App\LeaderboardDetailsColumns;
use App\RankPoints;

class UpdateStats implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, WorksWithinDatabaseTransaction;

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
     * The leaderboard source used to determine the schema to generate rankings on.
     *
     * @var \App\LeaderboardSources
     */
    protected $leaderboard_source;

    /**
     * The date that stats will be generated for.
     *
     * @var \App\Dates
     */
    protected $date;

    /**
     * The instance of the redis facade for this job.
     *
     * @var \Illuminate\Support\Facades\Redis
     */
    protected $redis;

    /**
     * All details columns available in the application.
     *
     * @var array
     */
    protected $details_columns = [];

    /**
     * All release ids that were processed in this process.
     *
     * @var array
     */
    protected $release_ids_processed = [];

    /**
     * Create a new job instance.
     *
     * @param \App\LeaderboardSources $leaderboard_source The leaderboard source context for this process.
     * @param \App\Dates $date The date that this process works within the context of.
     * @return void
     */
    public function __construct(LeaderboardSources $leaderboard_source, Dates $date) {
        $this->leaderboard_source = $leaderboard_source;
        $this->date = $date;
    }

    /**
     * Retrieves the number of first place ranks by player and release.
     *
     * @return array
     */
    protected function getFirstPlaceRanksByPlayer(): array {
        $ungrouped_rows = LeaderboardEntries::getFirstPlaceRanksByPlayerQuery($this->leaderboard_source, $this->date)->get();

        $grouped_ranks = [];

        foreach($ungrouped_rows as $row) {
            $grouped_ranks[$row->player_id][$row->release_id] = $row->first_place_ranks;
        }

        return $grouped_ranks;
    }

    /**
     * Loads all PBs into redis for the players being processed in this instance.
     *
     * @return void
     */
    protected function loadPbStats(): void {
        $first_place_ranks_by_player = $this->getFirstPlaceRanksByPlayer();

        DB::beginTransaction();

        $cursor = new PostgresCursor(
            "{$this->leaderboard_source->name}_pb_player_stats",
            PlayerPbs::getPlayerStatsQuery($this->leaderboard_source, $this->date),
            10000
        );

        $redis_transaction = new PipelineTransaction($this->redis, 1000);

        foreach($cursor->getRecord() as $player_pb) {
            $this->release_ids_processed[$player_pb->release_id] = $player_pb->release_id;

            $player_ids_key = CacheNames::getStatsIndex();

            $redis_transaction->sAdd($player_ids_key, $player_pb->player_id);

            $first_place_ranks = $first_place_ranks_by_player[$player_pb->player_id][$player_pb->release_id] ?? 0;

            $release_key = CacheNames::getPlayerStats($player_pb->player_id, $player_pb->release_id);

            $release_record = [
                'player_id' => $player_pb->player_id,
                'release_id' => $player_pb->release_id,
                'pbs' => $player_pb->pbs,
                'leaderboards' => $player_pb->leaderboards,
                'first_place_ranks' => $first_place_ranks,
                'dailies' => $player_pb->dailies,
                'unseeded_pbs' => $player_pb->unseeded_pbs,
                'seeded_pbs' => $player_pb->seeded_pbs,
                'leaderboard_types' => $player_pb->leaderboard_types,
                'characters' => $player_pb->characters,
                'modes' => $player_pb->modes,
                'seeded_types' => $player_pb->seeded_types,
                'multiplayer_types' => $player_pb->multiplayer_types,
                'soundtracks' => $player_pb->soundtracks
            ];

            $overall_key = CacheNames::getPlayerStats($player_pb->player_id, 'overall');

            foreach($this->details_columns as $details_column) {
                $details_column_name = "details_{$details_column->name}";

                if(isset($player_pb->$details_column_name)) {
                    $release_record[$details_column_name] = $player_pb->$details_column_name;


                    if(is_float($player_pb->$details_column_name + 0)) {
                        $redis_transaction->hIncrByFloat($overall_key, $details_column_name, $player_pb->$details_column_name);
                    }
                    else {
                        $redis_transaction->hIncrBy($overall_key, $details_column_name, $player_pb->$details_column_name);
                    }
                }
            }

            $redis_transaction->hMSet($release_key, $release_record);

            $redis_transaction->hSetNx($overall_key, 'player_id', $player_pb->player_id);
            $redis_transaction->hIncrBy($overall_key, 'pbs', $player_pb->pbs);
            $redis_transaction->hIncrBy($overall_key, 'leaderboards', $player_pb->leaderboards);
            $redis_transaction->hIncrBy($overall_key, 'first_place_ranks', $first_place_ranks);
            $redis_transaction->hIncrBy($overall_key, 'dailies', $player_pb->dailies);
            $redis_transaction->hIncrBy($overall_key, 'unseeded_pbs', $player_pb->unseeded_pbs);
            $redis_transaction->hIncrBy($overall_key, 'seeded_pbs', $player_pb->seeded_pbs);
        }

        $redis_transaction->commit();

        DB::commit();
    }

    /**
     * Aggregates rank points of all leaderboard entries into redis for the players being processed in this instance.
     *
     * @return void
     */
    protected function aggregateLeaderboardRankPoints(): void {
        DB::beginTransaction();

        $cursor = new PostgresCursor(
            "{$this->leaderboard_source->name}_leaderboard_entry_player_stats",
            LeaderboardEntries::getPlayerStatsQuery($this->leaderboard_source, $this->date),
            10000
        );

        $redis_transaction = new PipelineTransaction($this->redis, 1000);

        foreach($cursor->getRecord() as $leaderboard_entry) {
            $rank_points = RankPoints::calculateFromRank($leaderboard_entry->rank);

            $release_key = CacheNames::getPlayerStats($leaderboard_entry->player_id, $leaderboard_entry->release);

            $leaderboard_type_points_name = "leaderboard-type_{$leaderboard_entry->leaderboard_type}_points";
            $character_points_name = "character_{$leaderboard_entry->character}_points";
            $release_points_name = "release_{$leaderboard_entry->release}_points";
            $mode_points_name = "mode_{$leaderboard_entry->mode}_points";
            $seeded_type_points_name = "seeded-type_{$leaderboard_entry->seeded_type}_points";
            $multiplayer_type_points_name = "multiplayer-type_{$leaderboard_entry->multiplayer_type}_points";
            $soundtrack_points_name = "soundtrack_{$leaderboard_entry->soundtrack}_points";

            $redis_transaction->hIncrByFloat($release_key, $leaderboard_type_points_name, $rank_points);
            $redis_transaction->hIncrByFloat($release_key, $character_points_name, $rank_points);
            $redis_transaction->hIncrByFloat($release_key, $mode_points_name, $rank_points);
            $redis_transaction->hIncrByFloat($release_key, $seeded_type_points_name, $rank_points);
            $redis_transaction->hIncrByFloat($release_key, $multiplayer_type_points_name, $rank_points);
            $redis_transaction->hIncrByFloat($release_key, $soundtrack_points_name, $rank_points);

            $overall_key = CacheNames::getPlayerStats($leaderboard_entry->player_id, 'overall');

            $redis_transaction->hIncrByFloat($overall_key, $leaderboard_type_points_name, $rank_points);
            $redis_transaction->hIncrByFloat($overall_key, $character_points_name, $rank_points);
            $redis_transaction->hIncrByFloat($overall_key, $release_points_name, $rank_points);
            $redis_transaction->hIncrByFloat($overall_key, $mode_points_name, $rank_points);
            $redis_transaction->hIncrByFloat($overall_key, $seeded_type_points_name, $rank_points);
            $redis_transaction->hIncrByFloat($overall_key, $multiplayer_type_points_name, $rank_points);
            $redis_transaction->hIncrByFloat($overall_key, $soundtrack_points_name, $rank_points);
        }

        $redis_transaction->commit();

        DB::commit();
    }

    /**
     * Retrieves the best ID for each supported leaderboard attribute.
     *
     * @param array $record The aggregated record from redis.
     * @return array
     */
    protected function getBestAttributes(array $record): array {
        $best_ids = [];

        foreach($record as $field_name => $field_value) {
            if(strpos($field_name, '_points') !== false) {
                $field_name_split = explode('_', $field_name);

                $attribute_name = $field_name_split[0];
                $attribute_value = $field_name_split[1];

                if(!isset($best_ids[$attribute_name]) || $best_ids[$attribute_name]['points'] < $field_value) {
                    $best_ids[$attribute_name] = [
                        'name' => $attribute_value,
                        'points' => $field_value
                    ];
                }
            }
        }

        return $best_ids;
    }

    /**
     * Stages aggregated stats to a temporary table in the database.
     *
     * @param array $entries The chunk
     * @param RecordQueue $insert_queue The insert queue used to add records to the database.
     * @return void
     */
    public function saveChunkToDatabase($entries, RecordQueue $insert_queue): void {
        if(!empty($entries)) {
            foreach($entries as $entry) {
                if(!empty($entry)) {
                    $best_ids = $this->getBestAttributes($entry);

                    $bests = [];

                    foreach($best_ids as $best_name => $best_details) {
                        $best_name = str_replace('-', '_', $best_name);

                        $bests[$best_name] = $best_details['name'];
                    }

                    $details = [];

                    foreach($this->details_columns as $details_column) {
                        $details_field_name = "details_{$details_column->name}";

                        if(isset($entry[$details_field_name])) {
                            $details[$details_column->name] = $entry[$details_field_name];
                        }
                    }

                    $record = [
                        'player_id' => $entry['player_id'],
                        'release_id' => $entry['release_id'] ?? NULL,
                        'pbs' => $entry['pbs'],
                        'leaderboards' => $entry['leaderboards'],
                        'first_place_ranks' => $entry['first_place_ranks'],
                        'dailies' => $entry['dailies'],
                        'seeded_pbs' => $entry['seeded_pbs'],
                        'unseeded_pbs' => $entry['unseeded_pbs'],
                        'bests' => json_encode($bests),
                        'leaderboard_types' => $entry['leaderboard_types'] ?? NULL,
                        'characters' => $entry['characters'] ?? NULL,
                        'modes' => $entry['modes'] ?? NULL,
                        'seeded_types' => $entry['seeded_types'] ?? NULL,
                        'multiplayer_types' => $entry['multiplayer_types'] ?? NULL,
                        'soundtracks' => $entry['soundtracks'] ?? NULL,
                        'details' => json_encode($details)
                    ];

                    $hash = substr(md5(json_encode($record)), 0, 8);

                    /*
                        Date is added after hashing to help detect if the stats record
                        is the same on any other date for the same release
                    */
                    $record['date_id'] = $this->date->id;
                    $record['hash'] = $hash;

                    $insert_queue->addRecord($record);
                }
            }
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    protected function handleDatabaseTransaction(): void {
        $this->details_columns = LeaderboardDetailsColumns::all();

        $this->redis = Redis::connection('player_stats');

        $database_selector = new DatabaseSelector($this->redis, new DateTime($this->date->name));

        $database_selector->run();

        $this->loadPbStats();

        $this->aggregateLeaderboardRankPoints();

        $player_ids_key = CacheNames::getStatsIndex();

        $player_ids = $this->redis->sMembers($player_ids_key);

        if(!empty($player_ids)) {
            DB::beginTransaction();

            PlayerStats::createTemporaryTable($this->leaderboard_source);

            $insert_queue = PlayerStats::getTempInsertQueue($this->leaderboard_source, 2000);

            $redis_transaction = new PipelineTransaction($this->redis, 1000);

            $callback = new CallbackHandler();

            $callback->setCallback([
                $this,
                'saveChunkToDatabase'
            ]);

            $callback->setArguments([
                $insert_queue
            ]);

            $redis_transaction->addCommitCallback($callback);

            foreach($player_ids as $player_id) {
                foreach($this->release_ids_processed as $release_id) {
                    $release_key = CacheNames::getPlayerStats($player_id, $release_id);

                    $redis_transaction->hGetAll($release_key);
                }

                $overall_key = CacheNames::getPlayerStats($player_id, 'overall');

                $redis_transaction->hGetAll($overall_key);
            }

            $redis_transaction->commit();

            $insert_queue->commit();

            PlayerStats::clear($this->leaderboard_source, $this->date);

            PlayerStats::saveNewTemp($this->leaderboard_source);

            DB::commit();
        }

        $this->redis->flushDb();
    }
}
