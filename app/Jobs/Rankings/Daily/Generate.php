<?php

namespace App\Jobs\Rankings\Daily;

use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;
use App\Components\RecordQueue;
use App\Components\CallbackHandler;
use App\Components\Redis\DatabaseSelector;
use App\Components\PostgresCursor;
use App\Components\Redis\Transaction\Pipeline as PipelineTransaction;
use App\Components\CacheNames\Rankings\Daily as CacheNames;
use App\DailyRankings;
use App\DailyRankingDayTypes;
use App\DailyRankingEntries;
use App\Leaderboards;
use App\LeaderboardEntries;
use App\RankPoints;
use App\Releases;
use App\Modes;
use App\Jobs\Rankings\Daily\Entries\Cache as CacheJob;

class Generate implements ShouldQueue {
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
    
    protected function flattenLeaderboardEntries(DateTime $earliest_start_date, array $day_types) {        
        $cursor = new PostgresCursor(
            'daily_ranking_leaderboard_entries', 
            LeaderboardEntries::getDailyRankingsQuery($earliest_start_date, $this->date),
            10000
        );
        
        $redis_transaction = new PipelineTransaction($this->redis, 1000);
        
        foreach($cursor->getRecord() as $leaderboard_entry) {
            foreach($day_types as $day_type) {
                $daily_ranking_entry_hash_name = CacheNames::getEntry(
                    $leaderboard_entry->release_id, 
                    $leaderboard_entry->mode_id, 
                    $day_type->id, 
                    $leaderboard_entry->steam_user_id
                );
                
                $redis_transaction->hSetNx(
                    CacheNames::getModes($leaderboard_entry->release_id), 
                    $leaderboard_entry->mode_id, 
                    $leaderboard_entry->mode_id
                );
                
                $redis_transaction->hSetNx(
                    CacheNames::getModeNumberOfDays($leaderboard_entry->release_id, $leaderboard_entry->mode_id), 
                    $day_type->id, 
                    $day_type->id
                );
                    
                $redis_transaction->hSetNx($daily_ranking_entry_hash_name, 'steam_user_id', $leaderboard_entry->steam_user_id);
                
                $rank = (int)$leaderboard_entry->rank;
                $score = (int)$leaderboard_entry->score;
                
                if($rank == 1) {
                    $redis_transaction->hIncrBy($daily_ranking_entry_hash_name, 'first_place_ranks', 1);
                }
                elseif($rank <= 5) {                    
                    $redis_transaction->hIncrBy($daily_ranking_entry_hash_name, 'top_5_ranks', 1);
                }
                elseif($rank <= 10) {                    
                    $redis_transaction->hIncrBy($daily_ranking_entry_hash_name, 'top_10_ranks', 1);
                }
                elseif($rank <= 20) {                    
                    $redis_transaction->hIncrBy($daily_ranking_entry_hash_name, 'top_20_ranks', 1);
                }
                elseif($rank <= 50) {                    
                    $redis_transaction->hIncrBy($daily_ranking_entry_hash_name, 'top_50_ranks', 1);
                }
                elseif($rank <= 100) {                    
                    $redis_transaction->hIncrBy($daily_ranking_entry_hash_name, 'top_100_ranks', 1);
                }
                
                $rank_points = RankPoints::calculateFromRank($rank);
                
                $redis_transaction->hIncrByFloat($daily_ranking_entry_hash_name, 'total_points', $rank_points);
                
                $redis_transaction->zIncrBy(
                    CacheNames::getTotalPoints(
                        $leaderboard_entry->release_id, 
                        $leaderboard_entry->mode_id, 
                        $day_type->id
                    ), 
                    $rank_points, 
                    $leaderboard_entry->steam_user_id
                );
                
                if($leaderboard_entry->is_win == 1) {
                    $redis_transaction->hIncrBy($daily_ranking_entry_hash_name, 'total_wins', 1);
                }
                
                $redis_transaction->hIncrBy($daily_ranking_entry_hash_name, 'total_score', $score);
                $redis_transaction->hIncrBy($daily_ranking_entry_hash_name, 'sum_of_ranks', $rank);
                $redis_transaction->hIncrBy($daily_ranking_entry_hash_name, 'total_dailies', 1);
            }
        }
        
        $redis_transaction->commit();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {        
        $releases = Releases::getAllByDate($this->date);

        if(!empty($releases)) {
            $this->redis = Redis::connection('daily_rankings');
        
            $day_types = DailyRankingDayTypes::getAllActiveForDate($this->date);
            $earliest_start_date = Releases::getEarliestStartDate($releases);

            /* ---------- Select the first unused database ---------- */
            
            $database_selector = new DatabaseSelector($this->redis, $this->date);
            
            $database_selector->run();
            
            
            /* ---------- Load leaderboard rankings into redis to flatten the data for each player ---------- */
            
            $this->flattenLeaderboardEntries($earliest_start_date, $day_types);

            
            /* ---------- Look through each release, mode, and day type to generate rankings and save flattened records to the database ---------- */
            
            DB::beginTransaction();
            
            $daily_ranking_ids_by_grouped = DailyRankings::getAllIdsByGroupedForDate($this->date);
            $modes = Modes::getAllById();
            
            DailyRankings::createTemporaryTable();
            DailyRankingEntries::createTemporaryTable();
            
            $rankings_insert_queue = DailyRankings::getTempInsertQueue(9000);
            $entries_insert_queue = DailyRankingEntries::getTempInsertQueue(4000);
            
            foreach($releases as $release) {                    
                $modes_used = $this->redis->hGetAll(CacheNames::getModes($release->release_id));
                
                if(!empty($modes_used)) {
                    foreach($modes_used as $mode_id) {                    
                        if(isset($modes[$mode_id])) {
                            $mode = $modes[$mode_id];

                            $daily_ranking_day_types_used = $this->redis->hGetAll(CacheNames::getModeNumberOfDays($release->release_id, $mode_id));
                        
                            if(!empty($daily_ranking_day_types_used)) {
                                foreach($daily_ranking_day_types_used as $daily_ranking_day_type_id) {
                                    if(isset($day_types[$daily_ranking_day_type_id])) {
                                        $day_type = $day_types[$daily_ranking_day_type_id];
                                        
                                        
                                        /* ---------- Generate Daily Rankings from Points ---------- */
                                        
                                        $total_points_entries = $this->redis->zRevRange(CacheNames::getTotalPoints(
                                                $release->release_id, 
                                                $mode_id, 
                                                $daily_ranking_day_type_id
                                            ), 
                                            0, 
                                            -1
                                        );

                                        if(!empty($total_points_entries)) {
                                            $redis_transaction = new PipelineTransaction($this->redis, 1000);
                                        
                                            foreach($total_points_entries as $rank => $steam_user_id) {        
                                                $real_rank = $rank + 1;

                                                $redis_transaction->hSet(
                                                    CacheNames::getEntry(
                                                        $release->release_id, 
                                                        $mode_id, 
                                                        $daily_ranking_day_type_id, 
                                                        $steam_user_id
                                                    ), 
                                                    'rank', 
                                                    $real_rank
                                                );
                                            }
                                            
                                            $redis_transaction->commit();
                                        }
                                        
                                        
                                        /* ---------- Daily Rankings table ---------- */
                                    
                                        $daily_ranking_id = NULL;
                            
                                        $ranking_record = [
                                            'date' => $this->date->format('Y-m-d'),
                                            'release_id' => $release->release_id,
                                            'mode_id' => $mode->id,
                                            'daily_ranking_day_type_id' => $daily_ranking_day_type_id,
                                            'created' => date('Y-m-d H:i:s'),
                                            'updated' => NULL
                                        ];
                                        
                                        if(isset($daily_ranking_ids_by_grouped[$release->release_id][$mode->id][$daily_ranking_day_type_id])) {
                                            $daily_ranking_id = $daily_ranking_ids_by_grouped[$release->release_id][$mode->id][$daily_ranking_day_type_id];
                                            
                                            $ranking_record['updated'] = date('Y-m-d H:i:s');
                                        }
                                        else {
                                            $daily_ranking_id = DailyRankings::getNewRecordId();
                                            
                                            $daily_ranking_ids_by_grouped[$release->release_id][$mode->id][$daily_ranking_day_type_id] = $daily_ranking_id;
                                        }
                                        
                                        $ranking_record['daily_ranking_id'] = $daily_ranking_id;
                                        
                                        $rankings_insert_queue->addRecord($ranking_record);
                                        
                                        
                                        /* ---------- Save flattened cache entries into the database ---------- */
                                        
                                        if(!empty($total_points_entries)) {
                                            $redis_transaction = new PipelineTransaction($this->redis, 1000);
                                            
                                            $callback = new CallbackHandler();
                                            
                                            $callback->setCallback(function($entries, int $daily_ranking_id, RecordQueue $entries_insert_queue) {
                                                if(!empty($entries)) {
                                                    foreach($entries as $entry) {                                                        
                                                        $entries_insert_queue->addRecord([
                                                            'daily_ranking_id' => $daily_ranking_id,
                                                            'steam_user_id' => $entry['steam_user_id'],
                                                            'first_place_ranks' => $entry['first_place_ranks'] ?? 0,
                                                            'top_5_ranks' => $entry['top_5_ranks'] ?? 0,
                                                            'top_10_ranks' => $entry['top_10_ranks'] ?? 0,
                                                            'top_20_ranks' => $entry['top_20_ranks'] ?? 0,
                                                            'top_50_ranks' => $entry['top_50_ranks'] ?? 0,
                                                            'top_100_ranks' => $entry['top_100_ranks'] ?? 0,
                                                            'total_points' => $entry['total_points'],
                                                            'total_dailies' => $entry['total_dailies'],
                                                            'total_wins' => $entry['total_wins'] ?? 0,
                                                            'sum_of_ranks' => $entry['sum_of_ranks'],
                                                            'total_score' => $entry['total_score'],
                                                            'rank' => $entry['rank']
                                                        ]);
                                                    }
                                                }
                                            });
                                        }
                                        
                                        $callback->setArguments([
                                            $daily_ranking_id,
                                            $entries_insert_queue,
                                        ]);
                                        
                                        $redis_transaction->addCommitCallback($callback);
            
                                        foreach($total_points_entries as $rank => $steam_user_id) {
                                            $redis_transaction->hGetAll(CacheNames::getEntry(
                                                $release->release_id, 
                                                $mode_id, 
                                                $daily_ranking_day_type_id, 
                                                $steam_user_id
                                            ));
                                        }
                                        
                                        $redis_transaction->commit();
                                    }
                                }
                            }
                        }
                    }
                }
            }
            
            $rankings_insert_queue->commit();
            $entries_insert_queue->commit();
            
            DailyRankingEntries::clear($this->date);
            
            DailyRankings::saveTemp();
            DailyRankingEntries::saveTemp($this->date);
        
            DB::commit();
            
            CacheJob::dispatch($this->date);
        }
    
        $this->redis->flushDb();
    }
}
