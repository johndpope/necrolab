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
use App\Components\PostgresCursor;
use App\Components\Encoder;
use App\Components\RecordQueue;
use App\Components\InsertQueue;
use App\PowerRankings;
use App\PowerRankingEntries;
use App\LeaderboardTypes;

class UpdateStats implements ShouldQueue {
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

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        DB::beginTransaction();
        
        $leaderboard_types_by_name = LeaderboardTypes::getAllByName();
        
        $cursor = new PostgresCursor(
            'power_rankings_stats_update', 
            PowerRankingEntries::getStatsReadQuery($this->date),
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
        
            foreach($leaderboard_types_by_name as $leaderboard_type) {
                $rank_field_name = "{$leaderboard_type->name}_rank";
                
                if(!empty($power_ranking_entry->$rank_field_name)) {
                    if(!isset($category_summary[$power_ranking_id][$leaderboard_type->name]['players'])) {
                        $category_summary[$power_ranking_id][$leaderboard_type->name]['players'] = 0;
                    }
                    
                    $category_summary[$power_ranking_id][$leaderboard_type->name]['players'] += 1;
                }
            }
        
            $character_rankings = Encoder::decode(stream_get_contents($power_ranking_entry->characters));
            
            foreach($character_rankings as $character_name => $character_ranking) {
                /* ---------- Summarize character players ---------- */
            
                if(!isset($character_summary[$power_ranking_id][$character_name]['players'])) {
                    $character_summary[$power_ranking_id][$character_name]['players'] = 0;
                }
                
                $character_summary[$power_ranking_id][$character_name]['players'] += 1;
            
                foreach($character_ranking as $category_name => $category_data) {
                    if(isset($leaderboard_types_by_name[$category_name])) {
                        $details_field_name = $leaderboard_types_by_name[$category_name]->details_field_name;
                    
                    
                        /* ---------- Summarize character category players ---------- */
                        
                        if(!isset($character_summary[$power_ranking_id][$character_name][$category_name]['players'])) {
                            $character_summary[$power_ranking_id][$character_name][$category_name]['players'] = 0;
                        }
                        
                        $character_summary[$power_ranking_id][$character_name][$category_name]['players'] += 1;

                        $category_details = $category_data[$details_field_name];
                    
                    
                        /* ---------- Summarize category details (score, time, win_count, etc.) ---------- */
                        
                        if(!isset($category_summary[$power_ranking_id][$category_name][$details_field_name])) {
                            $category_summary[$power_ranking_id][$category_name][$details_field_name] = 0;
                        }
                        
                        $category_summary[$power_ranking_id][$category_name][$details_field_name] += $category_details;
                        
                        
                        /* ---------- Summarize character category details (score, time, win_count, etc.) ---------- */
                        
                        if(!isset($character_summary[$power_ranking_id][$character_name][$category_name][$details_field_name])) {
                            $character_summary[$power_ranking_id][$character_name][$category_name][$details_field_name] = 0;
                        }
                        
                        $character_summary[$power_ranking_id][$character_name][$category_name][$details_field_name] += $category_details;
                    }
                }
            }
        }
        
        PowerRankings::createTemporaryTable();
        
        
        /* ---------- Configure the record queue and insert queue ---------- */
        
        $record_queue = new RecordQueue(10000);
        
        $insert_queue = new InsertQueue(PowerRankings::getTempTableName());
        
        $insert_queue->setParameterBindings([
            PDO::PARAM_INT,
            PDO::PARAM_INT,
            PDO::PARAM_LOB,
            PDO::PARAM_LOB
        ]);
        
        $insert_queue->addToRecordQueue($record_queue);
        
        
        /* ---------- Add all records into the record queue ---------- */
        
        if(!empty($power_ranking_ids)) {
            foreach($power_ranking_ids as $power_ranking_id) {
                $record_queue->addRecord([
                    'power_ranking_id' => $power_ranking_id,
                    'players' => $players[$power_ranking_id],
                    'categories' => Encoder::encode($category_summary[$power_ranking_id]),
                    'characters' => Encoder::encode($character_summary[$power_ranking_id])
                ]);
            }
        }
        
        
        /* ---------- Save and commit ---------- */
        
        $record_queue->commit();
        
        PowerRankings::updateFromTemp();
        
        DB::commit();
    }
}
