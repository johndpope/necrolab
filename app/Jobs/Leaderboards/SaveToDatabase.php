<?php

namespace App\Jobs\Leaderboards;

use Throwable;
use DateTime;
use stdClass;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use App\Jobs\Traits\WorksWithinDatabaseTransaction;
use App\Components\QueueNames;
use App\Components\DataManagers\Leaderboards as DataManager;
use App\Dates;
use App\DailyDateFormats;
use App\LeaderboardSources;
use App\Leaderboards;
use App\LeaderboardRankingTypes;
use App\LeaderboardSnapshots;
use App\LeaderboardEntries;
use App\LeaderboardEntryDetails;
use App\LeaderboardDetailsColumns;
use App\DataTypes;
use App\Players;
use App\Replays;
use App\PlayerPbs;
use App\Jobs\Rankings\Power\Generate as PowerRankingsGenerateJob;
use App\Jobs\Rankings\Daily\Generate as DailyRankingsGenerateJob;
use App\Jobs\Players\Cache as PlayersCacheJob;
use App\Jobs\Leaderboards\Entries\CacheNonDaily as CacheNonDailyLeadeboardEntriesJob;
use App\Jobs\Leaderboards\Entries\CacheDaily as CacheDailyLeadeboardEntriesJob;
use App\Jobs\Leaderboards\Entries\UpdateStats AS UpdateStatsJob;
use App\Jobs\Players\UpdateStats AS UpdatePlayerStatsJob;

class SaveToDatabase implements ShouldQueue {
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
     * The date that leaderboard data is being imported for.
     *
     * @var DateTime
     */
    protected $date;

    /**
     * The date record that leaderboard data is being imported for.
     *
     * @var \App\Dates
     */
    protected $date_record;

    /**
     * The date that leaderboard data is being imported for.
     *
     * @var \App\Components\DataManagers\Leaderboards
     */
    protected $data_manager;

    /**
     * The leaderboard source that data is being saved to.
     *
     * @var \App\LeaderboardSources
     */
    protected $leaderboard_source;

    /**
     * The daily date format record used to detect daily leaderboards
     *
     * @var \App\DailyDateFormats
     */
    protected $daily_date_format;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(DataManager $data_manager) {
        $this->data_manager = $data_manager;

        $this->date = $this->data_manager->getDate();

        $this->leaderboard_source = $this->data_manager->getLeaderboardSource();

        $this->daily_date_format = DailyDateFormats::where('leaderboard_source_id', $this->leaderboard_source->id)->first();
    }

    /**
     * Saves leaderboard entries to the database.
     *
     * @return void
     */
    protected function handleDatabaseTransaction(): void {
        $current_date = new DateTime($this->date->name);

        $this->data_manager->deleteTemp();

        $this->data_manager->decompressToTemp();

        $files = $this->data_manager->getTempFiles();

        if(!empty($files)) {
            DB::beginTransaction();

            Players::createTemporaryTable($this->leaderboard_source);
            Replays::createTemporaryTable($this->leaderboard_source);
            PlayerPbs::createTemporaryTable($this->leaderboard_source);
            Leaderboards::createTemporaryTable($this->leaderboard_source);
            LeaderboardSnapshots::createTemporaryTable($this->leaderboard_source);
            LeaderboardRankingTypes::createTemporaryTable($this->leaderboard_source);
            LeaderboardEntries::createTemporaryTable($this->leaderboard_source);
            LeaderboardEntryDetails::createTemporaryTable($this->leaderboard_source);

            $ids_by_external_id = Leaderboards::getIdsByExternalId($this->leaderboard_source);
            $snapshot_ids_by_leaderboard_id = LeaderboardSnapshots::getAllByLeaderboardIdForDate($this->leaderboard_source, $this->date);
            $player_ids_by_external = Players::getAllIdsByPlayerid($this->leaderboard_source);
            $entry_details_by_name = LeaderboardEntryDetails::getAllByName($this->leaderboard_source);
            $player_pb_ids_by_unique = PlayerPbs::getAllIdsByUnique($this->leaderboard_source);

            $players_insert_queue = Players::getTempInsertQueue($this->leaderboard_source, 15000);
            $replays_insert_queue = Replays::getTempInsertQueue($this->leaderboard_source, 10000);
            $player_pbs_insert_queue = PlayerPbs::getTempInsertQueue($this->leaderboard_source, 5500);
            $leaderboards_insert_queue = Leaderboards::getTempInsertQueue($this->leaderboard_source, 5000);
            $leaderboard_snapshots_insert_queue = LeaderboardSnapshots::getTempInsertQueue($this->leaderboard_source, 12000);
            $leaderboard_ranking_types_insert_queue = LeaderboardRankingTypes::getTempInsertQueue($this->leaderboard_source, 32000);
            $leaderboard_entries_insert_queue = LeaderboardEntries::getTempInsertQueue($this->leaderboard_source, 15000);
            $leaderboard_entry_details_insert_queue = LeaderboardEntryDetails::getTempInsertQueue($this->leaderboard_source, 30000);

            foreach($this->data_manager->getTempLeaderboard() as $leaderboard) {
                /* ---------- Leaderboards ---------- */

                $leaderboard_id = NULL;

                Leaderboards::setPropertiesFromName($this->leaderboard_source, $leaderboard, $this->daily_date_format);

                $is_valid = Leaderboards::isValid($this->leaderboard_source, $leaderboard, $current_date);

                if($is_valid) {
                    if(isset($ids_by_external_id[$leaderboard->external_id])) {
                        $leaderboard_id = $ids_by_external_id[$leaderboard->external_id];
                    }
                    else {
                        $leaderboard_id = Leaderboards::getNewRecordId($this->leaderboard_source);

                        /* ---------- Leaderboard Ranking Types ---------- */

                        if(!empty($leaderboard->ranking_types)) {
                            foreach($leaderboard->ranking_types as $ranking_type) {
                                $leaderboard_ranking_types_insert_queue->addRecord([
                                    'leaderboard_id' => $leaderboard_id,
                                    'ranking_type_id' => $ranking_type->id
                                ]);
                            }
                        }

                        $leaderboards_insert_queue->addRecord([
                            'id' => $leaderboard_id,
                            'external_id' => $leaderboard->external_id,
                            'name' => $leaderboard->name,
                            'display_name' => $leaderboard->display_name,
                            'url' => $leaderboard->url,
                            'leaderboard_type_id' => $leaderboard->leaderboard_type->id,
                            'character_id' => $leaderboard->character->id,
                            'release_id' => $leaderboard->release->id,
                            'mode_id' => $leaderboard->mode->id,
                            'seeded_type_id' => $leaderboard->seeded_type->id,
                            'multiplayer_type_id' => $leaderboard->multiplayer_type->id,
                            'soundtrack_id' => $leaderboard->soundtrack->id,
                            'daily_date_id' => $leaderboard->daily_date->id ?? NULL
                        ]);

                        $ids_by_external_id[$leaderboard->external_id] = $leaderboard_id;
                    }


                    /* ---------- Leaderboard Snapshots ---------- */

                    $leaderboard_snapshot_id = NULL;

                    $snapshot_record = [
                        'leaderboard_id' => $leaderboard_id,
                        'date_id' => $this->date->id,
                        'updated' => NULL
                    ];

                    if(isset($snapshot_ids_by_leaderboard_id[$leaderboard_id])) {
                        $leaderboard_snapshot_id = $snapshot_ids_by_leaderboard_id[$leaderboard_id];

                        $snapshot_record['updated'] = date('Y-m-d H:i:s');
                    }
                    else {
                        $leaderboard_snapshot_id = LeaderboardSnapshots::getNewRecordId($this->leaderboard_source);

                        $snapshot_ids_by_leaderboard_id[$leaderboard_id] = $leaderboard_snapshot_id;
                    }

                    $snapshot_record['created'] = date('Y-m-d H:i:s');
                    $snapshot_record['id'] = $leaderboard_snapshot_id;

                    $leaderboard_snapshots_insert_queue->addRecord($snapshot_record);


                    /* ---------- Loop Through Leaderboard Entries ---------- */

                    $rank = 1;

                    foreach($this->data_manager->getTempEntry($leaderboard->external_id) as $entry) {
                        /* ---------- Players ---------- */

                        $player_id = NULL;

                        if(isset($player_ids_by_external[$entry->player_external_id])) {
                            $player_id = $player_ids_by_external[$entry->player_external_id];
                        }
                        else {
                            $player_id = Players::getNewRecordId($this->leaderboard_source);

                            $updated = new DateTime('-31 day');

                            $players_insert_queue->addRecord(array(
                                'id' => $player_id,
                                'external_id' => $entry->player_external_id,
                                'updated' => $updated->format('Y-m-d H:i:s'),
                                'created' => date('Y-m-d H:i:s')
                            ));

                            $player_ids_by_external[$entry->player_external_id] = $player_id;
                        }


                        /* ---------- Leaderboard Entry Details ---------- */

                        $leaderboard_entry_details_id = NULL;

                        if(isset($entry_details_by_name[$entry->details])) {
                            $leaderboard_entry_details_id = $entry_details_by_name[$entry->details]->id;
                        }
                        else {
                            $leaderboard_entry_details_id = LeaderboardEntryDetails::getNewRecordId($this->leaderboard_source);

                            $leaderboard_entry_details_insert_queue->addRecord([
                                'id' => $leaderboard_entry_details_id,
                                'name' => $entry->details
                            ]);

                            $leaderboard_entry_details = new LeaderboardEntryDetails();

                            $leaderboard_entry_details->id = $leaderboard_entry_details_id;
                            $leaderboard_entry_details->name = $entry->details;

                            $entry_details_by_name[$entry->details] = $leaderboard_entry_details;
                        }


                        /* ---------- Player PBs ---------- */

                        $player_pb_id = NULL;
                        $pb_is_valid = false;

                        if(isset($player_pb_ids_by_unique[$leaderboard_id][$player_id][$entry->raw_score])) {
                            $player_pb_id = $player_pb_ids_by_unique[$leaderboard_id][$player_id][$entry->raw_score];

                            $pb_is_valid = true;
                        }
                        else {
                            $pb_is_valid = PlayerPbs::isValid($leaderboard, $entry->raw_score);

                            if($pb_is_valid) {
                                PlayerPbs::setPropertiesFromEntry($entry, $leaderboard, $current_date);

                                $player_pb_id = PlayerPbs::getNewRecordId($this->leaderboard_source);

                                $player_pbs_insert_queue->addRecord([
                                    'id' => $player_pb_id,
                                    'player_id' => $player_id,
                                    'leaderboard_id' => $leaderboard_id,
                                    'first_leaderboard_snapshot_id' => $leaderboard_snapshot_id,
                                    'first_rank' => $rank,
                                    'leaderboard_entry_details_id' => $leaderboard_entry_details_id,
                                    'zone' => $entry->zone,
                                    'level' => $entry->level,
                                    'is_win' => $entry->is_win,
                                    'raw_score' => $entry->raw_score,
                                    'details' => json_encode($entry->details)
                                ]);

                                $player_pb_ids_by_unique[$leaderboard_id][$player_id][$entry->raw_score] = $player_pb_id;


                                /* ---------- Steam Replays ---------- */

                                $replays_insert_queue->addRecord([
                                    'player_pb_id' => $player_pb_id,
                                    'external_id' => $entry->replay_external_id,
                                    'player_id' => $player_id,
                                    'downloaded' => 0,
                                    'invalid' => 0,
                                    'uploaded_to_s3' => 0
                                ]);
                            }
                        }


                        /* ---------- Leaderboard Entry ---------- */

                        if($pb_is_valid) {
                            $leaderboard_entries_insert_queue->addRecord(array(
                                'leaderboard_snapshot_id' => $leaderboard_snapshot_id,
                                'player_pb_id' => $player_pb_id,
                                'player_id' => $player_id,
                                'rank' => $rank
                            ));

                            $rank += 1;
                        }
                    }
                }
            }

            // Commit the last of the queued records to go into the database
            $players_insert_queue->commit();
            $replays_insert_queue->commit();
            $player_pbs_insert_queue->commit();
            $leaderboards_insert_queue->commit();
            $leaderboard_snapshots_insert_queue->commit();
            $leaderboard_ranking_types_insert_queue->commit();
            $leaderboard_entries_insert_queue->commit();
            $leaderboard_entry_details_insert_queue->commit();

            // Remove all existing leaderboard entries for this date
            LeaderboardEntries::clear($this->leaderboard_source, $this->date);

            // Save all imported data to their permanent locations
            Leaderboards::saveNewTemp($this->leaderboard_source);
            LeaderboardRankingTypes::saveNewTemp($this->leaderboard_source);
            LeaderboardSnapshots::saveNewTemp($this->leaderboard_source);
            Players::saveNewTemp($this->leaderboard_source);
            LeaderboardEntryDetails::saveNewTemp($this->leaderboard_source);
            PlayerPbs::saveNewTemp($this->leaderboard_source);
            Replays::saveNewTemp($this->leaderboard_source);
            LeaderboardEntries::saveNewTemp($this->leaderboard_source, $current_date);

            DB::commit();

            // Dispatch all asynchronous jobs that utilize this imported data
            PowerRankingsGenerateJob::dispatch($this->leaderboard_source, $this->date)->onQueue(QueueNames::POWER_RANKINGS);
            DailyRankingsGenerateJob::dispatch($this->leaderboard_source, $this->date)->onQueue(QueueNames::DAILY_RANKINGS);
            UpdateStatsJob::dispatch($this->leaderboard_source, $this->date)->onQueue(QueueNames::LEADERBOARDS);
            PlayersCacheJob::dispatch($this->leaderboard_source)->onQueue(QueueNames::PLAYERS);
            UpdatePlayerStatsJob::dispatch($this->leaderboard_source, $this->date)->onQueue(QueueNames::PLAYERS);
            CacheNonDailyLeadeboardEntriesJob::dispatch($this->leaderboard_source, $this->date)->onQueue(QueueNames::LEADERBOARDS);
            CacheDailyLeadeboardEntriesJob::dispatch($this->leaderboard_source, $this->date)->onQueue(QueueNames::LEADERBOARDS);

            // Remove local temporary files
            $this->data_manager->deleteTemp();
        }
    }
}
