<?php

namespace App\Jobs\Legacy\Pbs;

use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use App\Components\PostgresCursor;
use App\LeaderboardSources;
use App\PlayerPbs;
use App\Leaderboards;

class Import implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;
    
    protected $leaderboard_source;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct() {}
    
    protected function importPlayerPbs(): void {
        $leaderboard_ids_by_external_id = Leaderboards::getIdsByExternalId($this->leaderboard_source);
    
        PlayerPbs::createTemporaryTable($this->leaderboard_source);
        
        $insert_queue = PlayerPbs::getTempInsertQueue($this->leaderboard_source, 5000);
        
        $legacy_cursor = new PostgresCursor('legacy_player_pbs', PlayerPbs::getLegacyImportQuery(), 10000);
        
        $legacy_cursor->setConnection('legacy');

        foreach($legacy_cursor->getRecord() as $legacy_record) {
            if(isset($leaderboard_ids_by_external_id[$legacy_record->lbid])) {
                $details = [];
                
                if(!empty($legacy_record->is_speedrun)) {                
                    $details['time'] = PlayerPbs::getTime($legacy_record->score);
                }
                
                if(!empty($legacy_record->is_deathless)) {                
                    $details['win_count'] = PlayerPbs::getWinCount($legacy_record->score);
                }
                
                if(empty($details)) {
                    $details['score'] = $legacy_record->score;
                }
            
                $insert_queue->addRecord([
                    'id' => $legacy_record->steam_user_pb_id,
                    'player_id' => $legacy_record->steam_user_id,
                    'leaderboard_id' => $legacy_record->leaderboard_id,
                    'first_leaderboard_snapshot_id' => $legacy_record->first_leaderboard_snapshot_id,
                    'first_rank' => $legacy_record->first_rank,
                    'leaderboard_entry_details_id' => $legacy_record->leaderboard_entry_details_id,
                    'zone' => (int)$legacy_record->zone,
                    'level' => (int)$legacy_record->level,
                    'is_win' => $legacy_record->is_win,
                    'raw_score' => $legacy_record->score,
                    'details' => json_encode($details)
                ]);
            }
        }
        
        $insert_queue->commit();
        
        PlayerPbs::saveNewTemp($this->leaderboard_source);
        
        PlayerPbs::syncManualSequence($this->leaderboard_source);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        $this->leaderboard_source = LeaderboardSources::getByName('steam');
    
        DB::beginTransaction();
        DB::connection('legacy')->beginTransaction();
        
        $this->importPlayerPbs();
        
        DB::connection('legacy')->commit();
        DB::commit();
    }
}
