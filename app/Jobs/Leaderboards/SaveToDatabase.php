<?php

namespace App\Jobs\Leaderboards;

use DateTime;
use stdClass;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use App\Components\SteamDataManager\Core as DataManager;
use App\Leaderboards;
use App\LeaderboardRankingTypes;
use App\LeaderboardSnapshots;
use App\LeaderboardEntries;
use App\LeaderboardEntryDetails;
use App\SteamUsers;
use App\SteamReplays;
use App\SteamUserPbs;
use App\Jobs\Rankings\Power\Generate as PowerRankingsGenerateJob;
use App\Jobs\Rankings\Daily\Generate as DailyRankingsGenerateJob;
use App\Jobs\SteamUsers\Cache as SteamUsersCacheJob;
use App\Jobs\SteamUserPbs\Cache as SteamUserPbsCacheJob;
use App\Jobs\Leaderboards\Entries\CacheNonDaily as CacheNonDailyLeadeboardEntriesJob;
use App\Jobs\Leaderboards\Entries\CacheDaily as CacheDailyLeadeboardEntriesJob;
use App\Jobs\Leaderboards\Entries\AggregateStats AS AggregateStatsJob;

class SaveToDatabase implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;
    
    protected $date;
    
    protected $data_manager;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(DataManager $data_manager) {
        $this->data_manager = $data_manager;
    
        $this->date = $this->data_manager->getDate();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {        
        $this->data_manager->deleteTemp();
        
        $this->data_manager->decompressToTemp();
        
        $files = $this->data_manager->getTempFiles();
        
        if(!empty($files)) {
            DB::beginTransaction();
            
            SteamUsers::createTemporaryTable();
            SteamReplays::createTemporaryTable();
            SteamUserPbs::createTemporaryTable();
            Leaderboards::createTemporaryTable();
            LeaderboardSnapshots::createTemporaryTable();
            LeaderboardRankingTypes::createTemporaryTable();
            LeaderboardEntries::createTemporaryTable();
            LeaderboardEntryDetails::createTemporaryTable();
            
            $ids_by_lbid = Leaderboards::getIdsByLbid();
            $snapshot_ids_by_leaderboard_id = LeaderboardSnapshots::getAllByLeaderboardIdForDate($this->date);
            $steam_user_ids_by_steamid = SteamUsers::getAllIdsBySteamid();
            $entry_details_by_name = LeaderboardEntryDetails::getAllByName();
            $steam_user_pb_ids_by_unique = SteamUserPbs::getAllIdsByUnique();
            
            $steam_users_insert_queue = SteamUsers::getTempInsertQueue(20000);
            $steam_replays_insert_queue = SteamReplays::getTempInsertQueue(6000);
            $steam_user_pbs_insert_queue = SteamUserPbs::getTempInsertQueue(5000);
            $leaderboards_insert_queue = Leaderboards::getTempInsertQueue(5000);
            $leaderboard_snapshots_insert_queue = LeaderboardSnapshots::getTempInsertQueue(12000);
            $leaderboard_ranking_types_insert_queue = LeaderboardRankingTypes::getTempInsertQueue(30000);
            $leaderboard_entries_insert_queue = LeaderboardEntries::getTempInsertQueue(15000);
            $leaderboard_entry_details_insert_queue = LeaderboardEntryDetails::getTempInsertQueue(30000);
        
            foreach($this->data_manager->getTempLeaderboard() as $leaderboard) {            
                /* ---------- Leaderboards ---------- */

                $leaderboard_id = NULL;

                Leaderboards::setPropertiesFromName($leaderboard);
                
                if(Leaderboards::isValid($leaderboard, $this->date)) {
                    if(isset($ids_by_lbid[$leaderboard->lbid])) {
                        $leaderboard_id = $ids_by_lbid[$leaderboard->lbid];
                    }
                    else {
                        $leaderboard_id = Leaderboards::getNewRecordId();
                        
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
                            'leaderboard_id' => $leaderboard_id,
                            'lbid' => $leaderboard->lbid,
                            'name' => $leaderboard->name,
                            'display_name' => $leaderboard->display_name,
                            'url' => $leaderboard->url,
                            'character_id' => $leaderboard->character->character_id,
                            'leaderboard_type_id' => $leaderboard->leaderboard_type->leaderboard_type_id,
                            'release_id' => $leaderboard->release->release_id,
                            'mode_id' => $leaderboard->mode->mode_id,
                            'daily_date' => $leaderboard->daily_date,
                            'is_custom' => $leaderboard->is_custom,
                            'is_co_op' => $leaderboard->is_co_op,
                            'is_seeded' => $leaderboard->is_seeded
                        ]);
                        
                        $ids_by_lbid[$leaderboard->lbid] = $leaderboard_id;
                    }
                    
                    /* ---------- Leaderboard Snapshots ---------- */
                    
                    $leaderboard_snapshot_id = NULL;
                    
                    $snapshot_record = [
                        'leaderboard_id' => $leaderboard_id,
                        'date' => $this->date->format('Y-m-d'),
                        'updated' => NULL
                    ];
                    
                    if(isset($snapshot_ids_by_leaderboard_id[$leaderboard_id])) {
                        $leaderboard_snapshot_id = $snapshot_ids_by_leaderboard_id[$leaderboard_id];
                        
                        $snapshot_record['updated'] = date('Y-m-d H:i:s');
                    }
                    else {
                        $leaderboard_snapshot_id = LeaderboardSnapshots::getNewRecordId();
                        
                        $snapshot_ids_by_leaderboard_id[$leaderboard_id] = $leaderboard_snapshot_id;
                    }
                    
                    $snapshot_record['created'] = date('Y-m-d H:i:s');
                    $snapshot_record['leaderboard_snapshot_id'] = $leaderboard_snapshot_id;
                    
                    $leaderboard_snapshots_insert_queue->addRecord($snapshot_record);
                    
                    /* ---------- Loop Through Leaderboard Entries ---------- */
                    
                    $rank = 1;

                    foreach($this->data_manager->getTempEntry($leaderboard->lbid) as $entry) {
                        /* ---------- Steam Users ---------- */
                        
                        $steam_user_id = NULL;
                        
                        if(isset($steam_user_ids_by_steamid[$entry->steamid])) {
                            $steam_user_id = $steam_user_ids_by_steamid[$entry->steamid];
                        }
                        else {                            
                            $steam_user_id = SteamUsers::getNewRecordId();
    
                            $updated = new DateTime('-31 day');
                            
                            $steam_users_insert_queue->addRecord(array(
                                'steam_user_id' => $steam_user_id,
                                'steamid' => $entry->steamid,
                                'updated' => $updated->format('Y-m-d H:i:s')
                            ));
                            
                            $steam_user_ids_by_steamid[$entry->steamid] = $steam_user_id;
                        }
                        
                        /* ---------- Leaderboard Entry Details ---------- */ 
                        
                        $leaderboard_entry_details_id = NULL;
                        
                        if(isset($entry_details_by_name[$entry->details])) {
                            $leaderboard_entry_details_id = $entry_details_by_name[$entry->details]->leaderboard_entry_details_id;
                        }
                        else {
                            $leaderboard_entry_details_id = LeaderboardEntryDetails::getNewRecordId();
                        
                            $leaderboard_entry_details_insert_queue->addRecord([
                                'leaderboard_entry_details_id' => $leaderboard_entry_details_id,
                                'name' => $entry->details
                            ]);
                            
                            $leaderboard_entry_details = new LeaderboardEntryDetails();
                            
                            $leaderboard_entry_details->leaderboard_entry_details_id = $leaderboard_entry_details_id;
                            $leaderboard_entry_details->name = $entry->details;
                            
                            $entry_details_by_name[$entry->details] = $leaderboard_entry_details;
                        }
                        
                        /* ---------- Steam User PBs ---------- */
                        
                        $steam_user_pb_id = NULL;
                        $pb_is_valid = false;
                        
                        if(isset($steam_user_pb_ids_by_unique[$leaderboard_id][$steam_user_id][$entry->score])) {
                            $steam_user_pb_id = $steam_user_pb_ids_by_unique[$leaderboard_id][$steam_user_id][$entry->score];
                            
                            $pb_is_valid = true;
                        }
                        else {
                            $pb_is_valid = SteamUserPbs::isValid($leaderboard, $entry->score);
                            
                            if($pb_is_valid) {
                                SteamUserPbs::setPropertiesFromEntry($entry, $leaderboard, $this->date);
                            
                                $steam_user_pb_id = SteamUserPbs::getNewRecordId();

                                $steam_user_pbs_insert_queue->addRecord([
                                    'steam_user_pb_id' => $steam_user_pb_id,
                                    'leaderboard_id' => $leaderboard_id,
                                    'steam_user_id' => $steam_user_id,
                                    'score' => $entry->score,
                                    'first_leaderboard_snapshot_id' => $leaderboard_snapshot_id,
                                    'first_rank' => $rank,
                                    'time' => $entry->time,
                                    'win_count' => $entry->win_count,
                                    'zone' => $entry->zone,
                                    'level' => $entry->level,
                                    'is_win' => $entry->is_win,
                                    'leaderboard_entry_details_id' => $leaderboard_entry_details_id
                                ]);
                                
                                $steam_user_pb_ids_by_unique[$leaderboard_id][$steam_user_id][$entry->score] = $steam_user_pb_id;

                                /* ---------- Steam Replays ---------- */
                                
                                $steam_replays_insert_queue->addRecord([
                                    'steam_user_pb_id' => $steam_user_pb_id,
                                    'ugcid' => $entry->ugcid,
                                    'steam_user_id' => $steam_user_id,
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
                                'steam_user_id' => $steam_user_id,
                                'steam_user_pb_id' => $steam_user_pb_id,
                                'rank' => $rank
                            ));
                            
                            $rank += 1;
                        }
                    }
                }
            }
            
            // Commit the last of the queued records to go into the database
            $steam_users_insert_queue->commit();
            $steam_replays_insert_queue->commit();
            $steam_user_pbs_insert_queue->commit();
            $leaderboards_insert_queue->commit();
            $leaderboard_snapshots_insert_queue->commit();
            $leaderboard_ranking_types_insert_queue->commit();
            $leaderboard_entries_insert_queue->commit();
            $leaderboard_entry_details_insert_queue->commit();
            
            // Remove all existing leaderboard entries for this date
            LeaderboardEntries::clear($this->date);
            
            // Save all imported data to their permanent locations
            Leaderboards::saveTemp();
            LeaderboardRankingTypes::saveTemp();
            LeaderboardSnapshots::saveTemp();
            SteamUsers::saveNewTemp();
            LeaderboardEntryDetails::saveTemp();
            SteamUserPbs::saveNewTemp();
            SteamReplays::saveNewTemp();
            LeaderboardEntries::saveTemp($this->date);
            
            DB::commit();
            
            // Dispatch all asynchronous jobs that utilize this imported data
            PowerRankingsGenerateJob::dispatch($this->date);
            DailyRankingsGenerateJob::dispatch($this->date);
            SteamUsersCacheJob::dispatch();
            SteamUserPbsCacheJob::dispatch();
            AggregateStatsJob::dispatch($this->date);
            CacheNonDailyLeadeboardEntriesJob::dispatch($this->date);
            CacheDailyLeadeboardEntriesJob::dispatch($this->date);
            
            // Remove local temporary files
            $this->data_manager->deleteTemp();
        }
    }
}
