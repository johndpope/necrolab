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
use App\Components\CacheNames\Prefix as CacheNamesPrefix;
use App\Components\Encoder;
use App\Dates;
use App\LeaderboardSources;
use App\LeaderboardTypes;
use App\DailyRankings;
use App\DailyRankingDayTypes;
use App\DailyRankingEntries;
use App\LeaderboardEntries;
use App\RankPoints;
use App\Jobs\Rankings\Daily\Entries\Cache as CacheJob;

class Generate implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;
    
    /**
     * The leaderboard source used to determine the schema to generate rankings on.
     *
     * @var \App\LeaderboardSources
     */
    protected $leaderboard_source;
    
    /**
     * The date that rankings will be generated for.
     *
     * @var \App\Dates
     */
    protected $date;
    
    /**
     * The day types records used for ranking day ranges.
     *
     * @var array
     */
    protected $daily_ranking_day_types = [];
    
    /**
     * The daily leaderboard type record.
     *
     * @var \App\LeaderboardTypes
     */
    protected $leaderboard_type;
    
    /**
     * The cache name prefixes used when flattening records
     *
     * @var array
     */
    protected $cache_name_prefixes = [];

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(LeaderboardSources $leaderboard_source, Dates $date) {
        $this->leaderboard_source = $leaderboard_source;
    
        $this->date = $date;
    }
    
    protected function flattenLeaderboardEntries() {
        $earliest_start_date = new DateTime($this->leaderboard_source->start_date);
        $end_date = new DateTime($this->date->name);
    
        $cursor = new PostgresCursor(
            'daily_ranking_leaderboard_entries', 
            LeaderboardEntries::getDailyRankingsQuery($this->leaderboard_source, $earliest_start_date, $end_date),
            10000
        );
        
        $redis_transaction = new PipelineTransaction($this->redis, 1000);
        
        $leaderboard_source_id = $this->leaderboard_source->id;
        
        foreach($cursor->getRecord() as $leaderboard_entry) {
            $entry_date = new DateTime($leaderboard_entry->date);

            foreach($this->daily_ranking_day_types as $day_type) {
                if($entry_date >= $day_type->start_date) {
                    $cache_name_prefix = new CacheNamesPrefix();
                
                    $cache_name_prefix->leaderboard_source_id = $leaderboard_source_id;
                    $cache_name_prefix->character_id =  $leaderboard_entry->character_id;
                    $cache_name_prefix->release_id =  $leaderboard_entry->release_id;
                    $cache_name_prefix->mode_id =  $leaderboard_entry->mode_id;
                    $cache_name_prefix->multiplayer_type_id = $leaderboard_entry->multiplayer_type_id;
                    $cache_name_prefix->soundtrack_id = $leaderboard_entry->soundtrack_id;
                    $cache_name_prefix->daily_ranking_day_type_id = $day_type->id;
                    
                    $this->cache_name_prefixes[(string)$cache_name_prefix] = $cache_name_prefix;
                
                    $daily_ranking_entry_hash_name = CacheNames::getEntry(
                        $cache_name_prefix,
                        $leaderboard_entry->player_id
                    );
                        
                    $redis_transaction->hSetNx($daily_ranking_entry_hash_name, 'player_id', $leaderboard_entry->player_id);
                    
                    $rank = (int)$leaderboard_entry->rank;
                    
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
                    
                    $redis_transaction->zIncrBy(
                        CacheNames::getTotalPoints($cache_name_prefix), 
                        $rank_points, 
                        $leaderboard_entry->player_id
                    );
                    
                    if($leaderboard_entry->is_win == 1) {
                        $redis_transaction->hIncrBy($daily_ranking_entry_hash_name, 'wins', 1);
                    }
                    
                    $redis_transaction->hIncrBy($daily_ranking_entry_hash_name, 'sum_of_ranks', $rank);
                    $redis_transaction->hIncrBy($daily_ranking_entry_hash_name, 'dailies', 1);
                    
                    // Add each details column to the record
                    $details = json_decode($leaderboard_entry->details, true);
                    
                    if(!empty($details)) {
                        foreach($details as $details_name => $details_value) {
                            $details_field_name = "details_{$details_name}";
                        
                            if(is_float($details_value)) {
                                $redis_transaction->hIncrByFloat($daily_ranking_entry_hash_name, $details_field_name, $details_value);
                            }
                            else {
                                $redis_transaction->hIncrBy($daily_ranking_entry_hash_name, $details_field_name, $details_value);
                            }
                        }
                    }
                }
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
        $this->leaderboard_type = LeaderboardTypes::where('name', 'daily')->firstOrFail();

        $this->redis = Redis::connection('daily_rankings');
        
        $this->daily_ranking_day_types = DailyRankingDayTypes::getAllByNameForDate($this->leaderboard_source, new DateTime($this->date->name));

        
        /* ---------- Select the first unused database ---------- */
        
        $database_selector = new DatabaseSelector($this->redis, new DateTime($this->date->name));
        
        $database_selector->run();
        
        
        DB::beginTransaction();
        
        
        /* ---------- Load leaderboard rankings into redis to flatten the data for each player ---------- */
        
        $this->flattenLeaderboardEntries();

        if(!empty($this->cache_name_prefixes)) {
            /* ---------- Look through each release, mode, and day type to generate rankings and save flattened records to the database ---------- */
            
            $daily_ranking_ids_by_grouped = DailyRankings::getAllIdsByGroupedForDate($this->leaderboard_source, $this->date);
            
            DailyRankings::createTemporaryTable($this->leaderboard_source);
            DailyRankingEntries::createTemporaryTable($this->leaderboard_source);
            
            $rankings_insert_queue = DailyRankings::getTempInsertQueue($this->leaderboard_source, 9000);
            $entries_insert_queue = DailyRankingEntries::getTempInsertQueue($this->leaderboard_source, 4000);
            
            foreach($this->cache_name_prefixes as $cache_name_prefix) {                               
                /* ---------- Generate Daily Rankings from Points ---------- */

                $total_points_entries = $this->redis->zRevRange(
                    CacheNames::getTotalPoints($cache_name_prefix), 
                    0, 
                    -1
                );

                if(!empty($total_points_entries)) {
                    $redis_transaction = new PipelineTransaction($this->redis, 1000);
                
                    foreach($total_points_entries as $rank => $player_id) {        
                        $real_rank = $rank + 1;

                        $redis_transaction->hSet(
                            CacheNames::getEntry(
                                $cache_name_prefix,
                                $player_id
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
                    'character_id' => $cache_name_prefix->character_id,
                    'release_id' => $cache_name_prefix->release_id,
                    'mode_id' => $cache_name_prefix->mode_id,
                    'multiplayer_type_id' => $cache_name_prefix->multiplayer_type_id,
                    'soundtrack_id' => $cache_name_prefix->soundtrack_id,
                    'daily_ranking_day_type_id' => $cache_name_prefix->daily_ranking_day_type_id,
                    'date_id' => $this->date->id,
                    'created' => date('Y-m-d H:i:s'),
                    'updated' => NULL
                ];
                
                if(isset($daily_ranking_ids_by_grouped[$cache_name_prefix->character_id][$cache_name_prefix->release_id][$cache_name_prefix->mode_id][$cache_name_prefix->multiplayer_type_id][$cache_name_prefix->soundtrack_id][$cache_name_prefix->daily_ranking_day_type_id])) {
                    $daily_ranking_id = $daily_ranking_ids_by_grouped[$cache_name_prefix->character_id][$cache_name_prefix->release_id][$cache_name_prefix->mode_id][$cache_name_prefix->multiplayer_type_id][$cache_name_prefix->soundtrack_id][$cache_name_prefix->daily_ranking_day_type_id];
                    
                    $ranking_record['updated'] = date('Y-m-d H:i:s');
                }
                else {
                    $daily_ranking_id = DailyRankings::getNewRecordId($this->leaderboard_source);
                    
                    $daily_ranking_ids_by_grouped[$cache_name_prefix->character_id][$cache_name_prefix->release_id][$cache_name_prefix->mode_id][$cache_name_prefix->multiplayer_type_id][$cache_name_prefix->soundtrack_id][$cache_name_prefix->daily_ranking_day_type_id] = $daily_ranking_id;
                }
                
                $ranking_record['id'] = $daily_ranking_id;
                
                $rankings_insert_queue->addRecord($ranking_record);
                
                
                /* ---------- Save flattened cache entries into the database ---------- */
                
                if(!empty($total_points_entries)) {
                    $redis_transaction = new PipelineTransaction($this->redis, 1000);
                    
                    $callback = new CallbackHandler();
                    
                    $callback->setCallback(function($entries, int $daily_ranking_id, RecordQueue $entries_insert_queue) {
                        if(!empty($entries)) {
                            foreach($entries as $entry) {
                                $details = [];
                                
                                foreach($entry as $field_name => $field_value) {
                                    if(strpos($field_name, 'details_') !== false) {
                                        $details_name = str_replace('details_', '', $field_name);
                                        
                                        $details[$details_name] = $field_value;
                                    }
                                }
                            
                                $entries_insert_queue->addRecord([
                                    'sum_of_ranks' => $entry['sum_of_ranks'],
                                    'daily_ranking_id' => $daily_ranking_id,
                                    'player_id' => $entry['player_id'],
                                    'rank' => $entry['rank'],
                                    'first_place_ranks' => $entry['first_place_ranks'] ?? 0,
                                    'top_5_ranks' => $entry['top_5_ranks'] ?? 0,
                                    'top_10_ranks' => $entry['top_10_ranks'] ?? 0,
                                    'top_20_ranks' => $entry['top_20_ranks'] ?? 0,
                                    'top_50_ranks' => $entry['top_50_ranks'] ?? 0,
                                    'top_100_ranks' => $entry['top_100_ranks'] ?? 0,
                                    'dailies' => $entry['dailies'],
                                    'wins' => $entry['wins'] ?? 0,
                                    'details' => Encoder::encode($details)
                                ]);
                            }
                        }
                    });
                    
                    $callback->setArguments([
                        $daily_ranking_id,
                        $entries_insert_queue,
                    ]);
                    
                    $redis_transaction->addCommitCallback($callback);

                    foreach($total_points_entries as $rank => $player_id) {
                        $redis_transaction->hGetAll(CacheNames::getEntry(
                            $cache_name_prefix,
                            $player_id
                        ));
                    }
                    
                    $redis_transaction->commit();
                }
            }
            
            $rankings_insert_queue->commit();
            $entries_insert_queue->commit();
            
            DailyRankingEntries::clear($this->leaderboard_source, $this->date);

            DailyRankings::saveNewTemp($this->leaderboard_source);
            DailyRankingEntries::saveNewTemp($this->leaderboard_source, $this->date);
            
            DB::commit();
            
            CacheJob::dispatch($this->leaderboard_source, $this->date);
        }
    
        $this->redis->flushDb();
    }
}
