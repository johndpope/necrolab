<?php

namespace App\Jobs\Rankings\Power;

use DateTime;
use Exception;
use PDO;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;
use App\Components\RecordQueue;
use App\Components\CallbackHandler;
use App\Components\PostgresCursor;
use App\Components\Redis\DatabaseSelector;
use App\Components\Redis\Transaction\Pipeline as PipelineTransaction;
use App\Components\CacheNames\Rankings\Power as CacheNames;
use App\PowerRankings;
use App\PowerRankingEntries;
use App\Leaderboards;
use App\LeaderboardEntries;
use App\RankPoints;
use App\Releases;
use App\Modes;
use App\Characters;
use App\Jobs\Rankings\Power\Entries\Cache as CacheJob;

class Generate implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;
    
    protected $date;
    
    protected $redis;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(DateTime $date) {
        $this->date = $date;
    }
    
    protected function flattenLeaderboardEntries() {
        $leaderboard_entries_query = LeaderboardEntries::getPowerRankingsQuery($this->date);
        
        $cursor = new PostgresCursor(
            'power_rankings_generate', 
            LeaderboardEntries::getPowerRankingsQuery($this->date),
            10000
        );
        
        $redis_transaction = new PipelineTransaction($this->redis, 1000);
        
        foreach($cursor->getRecord() as $leaderboard_entry) {
            // Add this mode to the list of ones that are being used in this release
            $redis_transaction->hSetNx(
                CacheNames::getModes(
                    $leaderboard_entry->release_id, 
                    $leaderboard_entry->is_seeded
                ), 
                $leaderboard_entry->mode_id, 
                $leaderboard_entry->mode_id
            );
            
            $pb_id_name = "{$leaderboard_entry->character_name}_{$leaderboard_entry->leaderboard_type}_pb_id";
            $rank_column_name = "{$leaderboard_entry->character_name}_{$leaderboard_entry->leaderboard_type}_rank";
            
            $rank = $leaderboard_entry->rank;
            $rank_points = RankPoints::calculateFromRank($rank);
            
            $power_ranking_entry_record = [];
            
            $power_ranking_entry_record['steam_user_id'] = $leaderboard_entry->steam_user_id;
            $power_ranking_entry_record[$pb_id_name] = $leaderboard_entry->steam_user_pb_id;
            $power_ranking_entry_record[$rank_column_name] = $rank;

            // Add the rank points for this record to the corresponding character
            $redis_transaction->zIncrBy(
                CacheNames::getCharacterPoints(
                    $leaderboard_entry->release_id, 
                    $leaderboard_entry->mode_id, 
                    $leaderboard_entry->is_seeded, 
                    $leaderboard_entry->character_name
                ), 
                $rank_points, 
                $leaderboard_entry->steam_user_id
            );
            
            // Add the rank points for this record to the total
            $redis_transaction->zIncrBy(
                CacheNames::getTotalPoints(
                    $leaderboard_entry->release_id, 
                    $leaderboard_entry->mode_id, 
                    $leaderboard_entry->is_seeded
                ), 
                $rank_points, 
                $leaderboard_entry->steam_user_id
            );
            
            switch($leaderboard_entry->leaderboard_type) {
                case 'speed':
                    // Add the rank points for this record to the speed ranking
                    $redis_transaction->zIncrBy(
                        CacheNames::getSpeedPoints(
                            $leaderboard_entry->release_id, 
                            $leaderboard_entry->mode_id, 
                            $leaderboard_entry->is_seeded
                        ), 
                        $rank_points, 
                        $leaderboard_entry->steam_user_id
                    );
                    break;
                case 'score':
                    // Add the rank points for this record to the score ranking
                    $redis_transaction->zIncrBy(
                        CacheNames::getScorePoints(
                            $leaderboard_entry->release_id, 
                            $leaderboard_entry->mode_id, 
                            $leaderboard_entry->is_seeded
                        ), 
                        $rank_points, 
                        $leaderboard_entry->steam_user_id
                    );
                    break;
                case 'deathless':
                    // Add the rank points for this record to the deathless ranking
                    $redis_transaction->zIncrBy(
                        CacheNames::getDeathlessPoints(
                            $leaderboard_entry->release_id, 
                            $leaderboard_entry->mode_id, 
                            $leaderboard_entry->is_seeded
                        ), 
                        $rank_points, 
                        $leaderboard_entry->steam_user_id
                    );
                    break;
            }
            
            // Merge the flattened fields to its corresponding record
            $redis_transaction->hMSet(
                CacheNames::getEntry(
                    $leaderboard_entry->release_id, 
                    $leaderboard_entry->mode_id, 
                    $leaderboard_entry->is_seeded, 
                    $leaderboard_entry->steam_user_id
                ), 
                $power_ranking_entry_record
            );
        }
        
        $redis_transaction->commit();
    }
    
    protected function generateRankPoints($points_hash_name, $release_id, $mode_id, $seeded, $rank_name) {
        $points_entries = $this->redis->zRevRange($points_hash_name, 0, -1);

        if(!empty($points_entries)) {
            $redis_transaction = new PipelineTransaction($this->redis, 1000);
        
            foreach($points_entries as $rank => $steam_user_id) {        
                $real_rank = $rank + 1;

                $redis_transaction->hSet(
                    CacheNames::getEntry(
                        $release_id, 
                        $mode_id, 
                        $seeded, 
                        $steam_user_id
                    ), 
                    $rank_name, 
                    $real_rank
                );
            }
            
            $redis_transaction->commit();
        }        
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        $this->redis = Redis::connection('power_rankings');

        /* ---------- Select the first unused database ---------- */
        
        $database_selector = new DatabaseSelector($this->redis, $this->date);
        
        $database_selector->run();
        
        
        /* ---------- Load leaderboard rankings into redis to flatten the data for each player ---------- */
        
        DB::beginTransaction();
        
        $this->flattenLeaderboardEntries();
        
        
        /* ---------- Retrieve flattened records from redis and save to the database ---------- */
        
        $releases = Releases::getAllByDate($this->date);
        
        if(!empty($releases)) {            
            $power_ranking_id_by_grouped = PowerRankings::getAllIdsByGroupedForDate($this->date);
            $modes = Modes::getAllById();
            $characters = Characters::getAllActive();
            
            PowerRankings::createTemporaryTable();
            PowerRankingEntries::createTemporaryTable();
            
            $rankings_insert_queue = PowerRankings::getTempInsertQueue(8000);
            $entries_insert_queue = PowerRankingEntries::getTempInsertQueue(9000);
            
            $seeded_flags = Leaderboards::getSeededFlags();
            
            foreach($releases as $release) {
                foreach($seeded_flags as $seeded) {
                    $modes_used = $this->redis->hGetAll(CacheNames::getModes($release->release_id, $seeded));
                    
                    if(!empty($modes_used)) {
                        foreach($modes_used as $mode_id) {                             
                            if(isset($modes[$mode_id])) {
                                $mode = $modes[$mode_id];                                
                                
                                /* ---------- Generate Score Rankings ---------- */
                                
                                $this->generateRankPoints(
                                    CacheNames::getScorePoints(
                                        $release->release_id, 
                                        $mode_id, 
                                        $seeded
                                    ),
                                    $release->release_id,
                                    $mode_id,
                                    $seeded,
                                    'score_rank'
                                );
                                
                                
                                /* ---------- Generate Speed Rankings ---------- */
                                
                                $this->generateRankPoints(
                                    CacheNames::getSpeedPoints(
                                        $release->release_id, 
                                        $mode_id, 
                                        $seeded
                                    ),
                                    $release->release_id,
                                    $mode_id,
                                    $seeded,
                                    'speed_rank'
                                );
                                
                                
                                /* ---------- Generate Deathless Rankings ---------- */
                                
                                $this->generateRankPoints(
                                    CacheNames::getDeathlessPoints(
                                        $release->release_id, 
                                        $mode_id, 
                                        $seeded
                                    ),
                                    $release->release_id,
                                    $mode_id,
                                    $seeded,
                                    'deathless_rank'
                                );
                                
                                
                                /* ---------- Generate Character Rankings ---------- */
                                
                                if(!empty($characters)) {
                                    foreach($characters as $character) {
                                        $this->generateRankPoints(
                                            CacheNames::getCharacterPoints(
                                                $release->release_id, 
                                                $mode_id, 
                                                $seeded,
                                                $character->name
                                            ),
                                            $release->release_id,
                                            $mode_id,
                                            $seeded,
                                            "{$character->name}_rank"
                                        );
                                    }
                                }
                                
                                
                                /* ---------- Generate Total Rankings ---------- */
                                
                                $this->generateRankPoints(
                                    CacheNames::getTotalPoints(
                                        $release->release_id, 
                                        $mode_id, 
                                        $seeded
                                    ),
                                    $release->release_id,
                                    $mode_id,
                                    $seeded,
                                    'rank'
                                );

                                /* ---------- Power Rankings table ---------- */
                                
                                $power_ranking_id = NULL;
                    
                                $ranking_record = [
                                    'date' => $this->date->format('Y-m-d'),
                                    'release_id' => $release->release_id,
                                    'mode_id' => $mode->mode_id,
                                    'seeded' => $seeded,
                                    'created' => date('Y-m-d H:i:s'),
                                    'updated' => NULL
                                ];
                                
                                if(isset($power_ranking_id_by_grouped[$release->release_id][$mode->mode_id][$seeded])) {
                                    $power_ranking_id = $power_ranking_id_by_grouped[$release->release_id][$mode->mode_id][$seeded];
                                    
                                    $ranking_record['updated'] = date('Y-m-d H:i:s');
                                }
                                else {
                                    $power_ranking_id = PowerRankings::getNewRecordId();
                                    
                                    $power_ranking_id_by_grouped[$release->release_id][$mode->mode_id][$seeded] = $power_ranking_id;
                                }
                                
                                $ranking_record['power_ranking_id'] = $power_ranking_id;
                                
                                $rankings_insert_queue->addRecord($ranking_record);
                                
                                
                                /* ---------- Save flattened cache entries into the database ---------- */
                                
                                $total_points_entries = $this->redis->zRevRange(
                                    CacheNames::getTotalPoints(
                                        $release->release_id, 
                                        $mode_id, 
                                        $seeded
                                    ),
                                    0, 
                                    -1
                                );
                                
                                if(!empty($total_points_entries)) {
                                    $redis_transaction = new PipelineTransaction($this->redis, 1000);
                                    
                                    $callback = new CallbackHandler();
                                    
                                    $callback->setCallback(function($entries, int $power_ranking_id, RecordQueue $entries_insert_queue, array $characters) {
                                        if(!empty($entries)) {
                                            foreach($entries as $entry) {
                                                if(!empty($entry)) {                                                                                                          
                                                    $entries_insert_queue->addRecord([
                                                        'power_ranking_id' => $power_ranking_id,
                                                        'steam_user_id' => $entry['steam_user_id'],
                                                        'characters' => PowerRankingEntries::serializeCharacters($entry, $characters),
                                                        'score_rank' => $entry['score_rank'] ?? NULL,
                                                        'speed_rank' => $entry['speed_rank'] ?? NULL,
                                                        'deathless_rank' => $entry['deathless_rank'] ?? NULL,
                                                        'rank' => $entry['rank']
                                                    ]);
                                                }
                                            }
                                        }
                                    });
                                    
                                    $callback->setArguments([
                                        $power_ranking_id,
                                        $entries_insert_queue,
                                        $characters
                                    ]);
                                    
                                    $redis_transaction->addCommitCallback($callback);
        
                                    foreach($total_points_entries as $rank => $steam_user_id) {
                                        $redis_transaction->hGetAll(CacheNames::getEntry($release->release_id, $mode_id, $seeded, $steam_user_id));
                                    }
                                    
                                    $redis_transaction->commit();
                                }
                            }
                        }
                    }
                }
            }
            
            $rankings_insert_queue->commit();
            $entries_insert_queue->commit();
            
            PowerRankingEntries::clear($this->date);
            
            PowerRankings::saveTemp();
            PowerRankingEntries::saveTemp($this->date);

            CacheJob::dispatch($this->date);
        }
        
        DB::commit();

        $this->redis->flushDb();
    }
}