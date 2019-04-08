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
use App\Components\QueueNames;
use App\Components\RecordQueue;
use App\Components\CallbackHandler;
use App\Components\PostgresCursor;
use App\Components\Redis\DatabaseSelector;
use App\Components\Redis\Transaction\Pipeline as PipelineTransaction;
use App\Components\CacheNames\Rankings\Power as CacheNames;
use App\Components\CacheNames\Prefix as CacheNamesPrefix;
use App\Components\Encoder;
use App\LeaderboardSources;
use App\Dates;
use App\LeaderboardTypes;
use App\PowerRankings;
use App\PowerRankingEntries;
use App\Leaderboards;
use App\LeaderboardEntries;
use App\RankPoints;
use App\Characters;
use App\Jobs\Rankings\Power\Entries\Cache as CacheJob;
use App\Jobs\Rankings\Power\Entries\UpdateStats as UpdateStatsJob;

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
     * The cache name prefixes used when flattening records
     *
     * @var array
     */
    protected $cache_name_prefixes = [];
    
    /**
     * Leaderboard types used to generate category rankings
     *
     * @var array
     */
    protected $leaderboard_types = [];
    
    protected $redis;

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
        $leaderboard_source_id = $this->leaderboard_source->id;
    
        $cursor = new PostgresCursor(
            'power_rankings_generate', 
            LeaderboardEntries::getPowerRankingsQuery($this->leaderboard_source, $this->date),
            10000
        );
        
        $redis_transaction = new PipelineTransaction($this->redis, 1000);
        
        foreach($cursor->getRecord() as $leaderboard_entry) {    
            $cache_name_prefix = new CacheNamesPrefix();
                
            $cache_name_prefix->leaderboard_source_id = $leaderboard_source_id;
            $cache_name_prefix->release_id =  $leaderboard_entry->release_id;
            $cache_name_prefix->mode_id =  $leaderboard_entry->mode_id;
            $cache_name_prefix->seeded_type_id =  $leaderboard_entry->seeded_type_id;
            $cache_name_prefix->multiplayer_type_id = $leaderboard_entry->multiplayer_type_id;
            $cache_name_prefix->soundtrack_id = $leaderboard_entry->soundtrack_id;
            
            $this->cache_name_prefixes[(string)$cache_name_prefix] = $cache_name_prefix;
        
            $pb_id_name = "{$leaderboard_entry->character}_{$leaderboard_entry->leaderboard_type}_pb_id";
            $rank_column_name = "{$leaderboard_entry->character}_{$leaderboard_entry->leaderboard_type}_rank";
            
            $rank = $leaderboard_entry->rank;
            $rank_points = RankPoints::calculateFromRank($rank);
            
            $power_ranking_entry_record = [];
            
            $power_ranking_entry_record['player_id'] = $leaderboard_entry->player_id;
            $power_ranking_entry_record[$pb_id_name] = $leaderboard_entry->player_pb_id;
            $power_ranking_entry_record[$rank_column_name] = $rank;

            // Add the rank points for this record to the corresponding character
            $redis_transaction->zIncrBy(
                CacheNames::getCharacterPoints(
                    $cache_name_prefix,
                    $leaderboard_entry->character
                ), 
                $rank_points, 
                $leaderboard_entry->player_id
            );
            
            // Add the rank points for this record to the total
            $redis_transaction->zIncrBy(
                CacheNames::getTotalPoints($cache_name_prefix), 
                $rank_points, 
                $leaderboard_entry->player_id
            );
            
            // Add the rank points for this record to it respective category ranking
            $redis_transaction->zIncrBy(
                CacheNames::getCategoryPoints($cache_name_prefix, $leaderboard_entry->leaderboard_type), 
                $rank_points, 
                $leaderboard_entry->player_id
            );
            
            $details = json_decode($leaderboard_entry->details, true);
            
            if(!empty($details)) {
                foreach($details as $details_name => $details_value) {
                    $details_field_name = "{$leaderboard_entry->character}_{$leaderboard_entry->leaderboard_type}_{$details_name}";
                    
                    $power_ranking_entry_record[$details_field_name] = $details_value;
                }
            }
            
            // Merge the flattened fields to its corresponding record
            $redis_transaction->hMSet(
                CacheNames::getEntry(
                    $cache_name_prefix,
                    $leaderboard_entry->player_id
                ), 
                $power_ranking_entry_record
            );
        }
        
        $redis_transaction->commit();
    }
    
    protected function generateRankPoints(string $points_hash_name, CacheNamesPrefix $cache_name_prefix, string $rank_name) {
        $points_entries = $this->redis->zRevRange($points_hash_name, 0, -1);

        if(!empty($points_entries)) {
            $redis_transaction = new PipelineTransaction($this->redis, 1000);
        
            foreach($points_entries as $rank => $player_id) {        
                $real_rank = $rank + 1;

                $redis_transaction->hSet(
                    CacheNames::getEntry(
                        $cache_name_prefix,
                        $player_id
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
        $this->leaderboard_types = LeaderboardTypes::getAllByName();
    
        $this->redis = Redis::connection('power_rankings');

        /* ---------- Select the first unused database ---------- */
        
        $database_selector = new DatabaseSelector($this->redis, new DateTime($this->date->name));
        
        $database_selector->run();
        
        
        /* ---------- Load leaderboard rankings into redis to flatten the data for each player ---------- */
        
        DB::beginTransaction();
        
        $this->flattenLeaderboardEntries();
        
        
        /* ---------- Retrieve flattened records from redis and save to the database ---------- */
        
        if(!empty($this->cache_name_prefixes)) {
            $power_ranking_id_by_grouped = PowerRankings::getAllIdsByGroupedForDate($this->leaderboard_source, $this->date);
            $characters = Characters::getAllByName();

            PowerRankings::createTemporaryTable($this->leaderboard_source);
            PowerRankingEntries::createTemporaryTable($this->leaderboard_source);
            
            $rankings_insert_queue = PowerRankings::getTempInsertQueue($this->leaderboard_source, 8000);
            $entries_insert_queue = PowerRankingEntries::getTempInsertQueue($this->leaderboard_source, 9000);
            
            foreach($this->cache_name_prefixes as $cache_name_prefix) {
                /* ---------- Generate Category Rankings ---------- */
                
                if(!empty($this->leaderboard_types)) {
                    foreach($this->leaderboard_types as $leaderboard_type) {
                        $this->generateRankPoints(
                            CacheNames::getCategoryPoints(
                                $cache_name_prefix,
                                $leaderboard_type->name
                            ),
                            $cache_name_prefix,
                            "{$leaderboard_type->name}_rank"
                        );
                    }
                }
                
                
                /* ---------- Generate Character Rankings ---------- */
                
                if(!empty($characters)) {
                    foreach($characters as $character) {
                        $this->generateRankPoints(
                            CacheNames::getCharacterPoints(
                                $cache_name_prefix,
                                $character->name
                            ),
                            $cache_name_prefix,
                            "{$character->name}_rank"
                        );
                    }
                }
                
                
                /* ---------- Generate Total Rankings ---------- */
                
                $this->generateRankPoints(
                    CacheNames::getTotalPoints($cache_name_prefix),
                    $cache_name_prefix,
                    'rank'
                );

                /* ---------- Power Rankings table ---------- */
                
                $power_ranking_id = NULL;
    
                $ranking_record = [
                    'created' => date('Y-m-d H:i:s'),
                    'updated' => NULL,
                    'release_id' => $cache_name_prefix->release_id,
                    'mode_id' => $cache_name_prefix->mode_id,
                    'seeded_type_id' => $cache_name_prefix->seeded_type_id,
                    'multiplayer_type_id' => $cache_name_prefix->multiplayer_type_id,
                    'soundtrack_id' => $cache_name_prefix->soundtrack_id,
                    'date_id' => $this->date->id
                ];
                
                if(isset($power_ranking_id_by_grouped[$cache_name_prefix->release_id][$cache_name_prefix->mode_id][$cache_name_prefix->seeded_type_id][$cache_name_prefix->multiplayer_type_id][$cache_name_prefix->soundtrack_id])) {
                    $power_ranking_id = $power_ranking_id_by_grouped[$cache_name_prefix->release_id][$cache_name_prefix->mode_id][$cache_name_prefix->seeded_type_id][$cache_name_prefix->multiplayer_type_id][$cache_name_prefix->soundtrack_id];
                    
                    $ranking_record['updated'] = date('Y-m-d H:i:s');
                }
                else {
                    $power_ranking_id = PowerRankings::getNewRecordId($this->leaderboard_source);
                    
                    $power_ranking_id_by_grouped[$cache_name_prefix->release_id][$cache_name_prefix->mode_id][$cache_name_prefix->seeded_type_id][$cache_name_prefix->multiplayer_type_id][$cache_name_prefix->soundtrack_id] = $power_ranking_id;
                }
                
                $ranking_record['id'] = $power_ranking_id;
                
                $rankings_insert_queue->addRecord($ranking_record);
                
                
                /* ---------- Save flattened cache entries into the database ---------- */
                
                $total_points_entries = $this->redis->zRevRange(
                    CacheNames::getTotalPoints($cache_name_prefix),
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
                                    $category_ranks = [];
                                
                                    if(!empty($this->leaderboard_types)) {
                                        foreach($this->leaderboard_types as $leaderboard_type_name => $leaderboard_type) {
                                            $category_rank_name = "{$leaderboard_type_name}_rank";
                                        
                                            if(isset($entry[$category_rank_name])) {
                                                $category_ranks[$leaderboard_type_name] = $entry[$category_rank_name];
                                            }
                                        }
                                    }
                                
                                    $entries_insert_queue->addRecord([
                                        'power_ranking_id' => $power_ranking_id,
                                        'player_id' => $entry['player_id'],
                                        'rank' => $entry['rank'],
                                        'characters' => PowerRankingEntries::serializeCharacters($entry, $characters, $this->leaderboard_types),
                                        'category_ranks' => Encoder::encode($category_ranks)
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

                    foreach($total_points_entries as $rank => $player_id) {
                        $redis_transaction->hGetAll(CacheNames::getEntry($cache_name_prefix, $player_id));
                    }
                    
                    $redis_transaction->commit();
                }
            }

            $rankings_insert_queue->commit();
            $entries_insert_queue->commit();

            PowerRankingEntries::clear($this->leaderboard_source, $this->date);

            PowerRankings::saveNewTemp($this->leaderboard_source);
            PowerRankingEntries::saveNewTemp($this->leaderboard_source, $this->date);

            CacheJob::dispatch($this->leaderboard_source, $this->date)->onQueue(QueueNames::POWER_RANKINGS);
            UpdateStatsJob::dispatch($this->leaderboard_source, $this->date)->onQueue(QueueNames::POWER_RANKINGS);
        }
        
        DB::commit();

        $this->redis->flushDb();
    }
}
