<?php

namespace App\Jobs\Rankings\Power\Entries;

use DateTime;
use PDO;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use App\Jobs\Traits\WorksWithinDatabaseTransaction;
use App\Components\PostgresCursor;
use App\Components\Encoder;
use App\LeaderboardSources;
use App\Dates;
use App\PowerRankings;
use App\PowerRankingEntries;
use App\LeaderboardTypes;

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
     * The date that rankings will be generated for.
     *
     * @var \App\Dates
     */
    protected $date;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(LeaderboardSources $leaderboard_source, Dates $date) {
        $this->leaderboard_source = $leaderboard_source;
    
        $this->date = $date;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    protected function handleDatabaseTransaction(): void {
        DB::beginTransaction();
        
        $leaderboard_types_by_name = LeaderboardTypes::getAllByName();
        
        $cursor = new PostgresCursor(
            'power_rankings_stats_update', 
            PowerRankingEntries::getStatsReadQuery($this->leaderboard_source, $this->date),
            10000
        );
        
        $power_ranking_ids = [];
        $players = [];
        $category_summary = [];
        $character_summary = [];
        
        foreach($cursor->getRecord() as $power_ranking_entry) {
            $power_ranking_id = $power_ranking_entry->power_ranking_id;
            
            $power_ranking_ids[$power_ranking_id] = $power_ranking_id;
            
            if(!isset($players[$power_ranking_id])) {
                $players[$power_ranking_id] = 0;
            }
        
            $players[$power_ranking_id] += 1;
        
        
            /* ---------- Summarize category players ---------- */
            
            $category_ranks = Encoder::decode(stream_get_contents($power_ranking_entry->category_ranks));

            if(!empty($category_ranks)) {
                foreach($category_ranks as $leaderboard_type_name => $category_rank) {
                    if(!isset($category_summary[$power_ranking_id][$leaderboard_type_name]['players'])) {
                        $category_summary[$power_ranking_id][$leaderboard_type_name]['players'] = 0;
                    }
                    
                    $category_summary[$power_ranking_id][$leaderboard_type_name]['players'] += 1;
                }
            }
        
            $character_rankings = Encoder::decode(stream_get_contents($power_ranking_entry->characters));

            if(!empty($character_rankings)) {
                foreach($character_rankings as $character_name => $character_ranking) {
                    /* ---------- Summarize character players ---------- */
                
                    if(!isset($character_summary[$power_ranking_id][$character_name]['players'])) {
                        $character_summary[$power_ranking_id][$character_name]['players'] = 0;
                    }
                    
                    $character_summary[$power_ranking_id][$character_name]['players'] += 1;
                    
                    
                    $character_categories = $character_ranking['categories'] ?? [];
                
                    if(!empty($character_categories)) {
                        foreach($character_categories as $leaderboard_type_name => $category_data) {
                            /* ---------- Summarize character category players ---------- */
                                
                            if(!isset($character_summary[$power_ranking_id][$character_name]['categories'][$leaderboard_type_name]['players'])) {
                                $character_summary[$power_ranking_id][$character_name]['categories'][$leaderboard_type_name]['players'] = 0;
                            }
                            
                            $character_summary[$power_ranking_id][$character_name]['categories'][$leaderboard_type_name]['players'] += 1;
                            
                        
                            $character_details = $category_data['details'] ?? [];
                        
                            if(!empty($character_details)) {
                                foreach($character_details as $details_name => $details_value) {
                                    /* ---------- Summarize category details ---------- */
                                
                                    if(!isset($category_summary[$power_ranking_id][$leaderboard_type_name]['details'][$details_name])) {
                                        $category_summary[$power_ranking_id][$leaderboard_type_name]['details'][$details_name] = 0;
                                    }
                                    
                                    $category_summary[$power_ranking_id][$leaderboard_type_name]['details'][$details_name] += $details_value;
                                
                                
                                    /* ---------- Summarize character category details ---------- */
                                
                                    if(!isset($character_summary[$power_ranking_id][$character_name]['categories'][$leaderboard_type_name]['details'][$details_name])) {
                                        $character_summary[$power_ranking_id][$character_name]['categories'][$leaderboard_type_name]['details'][$details_name] = 0;
                                    }
                                    
                                    $character_summary[$power_ranking_id][$character_name]['categories'][$leaderboard_type_name]['details'][$details_name] += $details_value;
                                }
                            }
                        }
                    }
                }
            }
        }
        
        PowerRankings::createTemporaryTable($this->leaderboard_source);
        
        
        /* ---------- Configure the record queue and insert queue ---------- */

        $insert_queue = PowerRankings::getTempInsertQueue($this->leaderboard_source, 8000);
        
        
        /* ---------- Add all records into the record queue ---------- */
        
        if(!empty($power_ranking_ids)) {
            foreach($power_ranking_ids as $power_ranking_id) {
                $insert_queue->addRecord([
                    'id' => $power_ranking_id,
                    'players' => $players[$power_ranking_id],
                    'categories' => json_encode($category_summary[$power_ranking_id] ?? []),
                    'characters' => json_encode($character_summary[$power_ranking_id] ?? [])
                ]);
            }
        }
        
        
        /* ---------- Save and commit ---------- */
        
        $insert_queue->commit();

        PowerRankings::updateFromTemp($this->leaderboard_source);
        
        DB::commit();
    }
}
