<?php

namespace App\Jobs\Replays;

use DateTime;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use App\Components\DataManagers\Replays as DataManager;
use App\Replays;
use App\RunResults;
use App\ReplayVersions;
use App\Seeds;

class SaveImported implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;
    
    /**
     * The replays data manager used to interact with imported replay files.
     *
     * @var \App\Components\DataManagers\Replays
     */
    protected $data_manager;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(DataManager $data_manager) {
        $this->data_manager = $data_manager;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        $leaderboard_source = $this->data_manager->getLeaderboardSource();
    
        DB::beginTransaction();
        
        Replays::createTemporaryTable($leaderboard_source);
        
        $replays_insert_queue = Replays::getTempInsertQueue($leaderboard_source, 10000);
        
        $temp_files = $this->data_manager->getTempFiles();
        
        if(!empty($temp_files)) { 
            unset($temp_files);
        
            $run_results_by_name = RunResults::getAllIdsByName($leaderboard_source);
            $replay_versions_by_name = ReplayVersions::getAllIdsByName($leaderboard_source);
            $seeds_by_name = Seeds::getAllIdsByName($leaderboard_source);
            
            RunResults::createTemporaryTable($leaderboard_source);
            ReplayVersions::createTemporaryTable($leaderboard_source);
            Seeds::createTemporaryTable($leaderboard_source);
            
            $run_results_insert_queue = RunResults::getTempInsertQueue($leaderboard_source, 20000);
            $replay_versions_insert_queue = ReplayVersions::getTempInsertQueue($leaderboard_source, 30000);
            $seeds_insert_queue = Seeds::getTempInsertQueue($leaderboard_source, 30000);
            

            foreach($this->data_manager->getTempFile() as $replay) {
                $replay_properties = Replays::getParsedReplayProperties($replay->contents);
                
                
                /* ---------- Run Results ---------- */
                
                $run_result_id = NULL;
                
                if(!isset($run_results_by_name[$replay_properties->run_result])) {
                    $run_result_id = RunResults::getNewRecordId($leaderboard_source);
                    
                    $run_results_insert_queue->addRecord([
                        'id' => $run_result_id,
                        'name' => $replay_properties->run_result,
                        'is_win' => $replay_properties->is_win
                    ]);
                    
                    $run_results_by_name[$replay_properties->run_result] = $run_result_id;
                }
                else {
                    $run_result_id = $run_results_by_name[$replay_properties->run_result];
                }
                
                
                /* ---------- Steam Replay Versions ---------- */
                
                $replay_version_id = NULL;
                
                if(!isset($replay_versions_by_name[$replay_properties->version])) {
                    $replay_version_id = ReplayVersions::getNewRecordId($leaderboard_source);
                    
                    $replay_versions_insert_queue->addRecord([
                        'id' => $replay_version_id,
                        'name' => $replay_properties->version,
                    ]);
                    
                    $replay_versions_by_name[$replay_properties->version] = $replay_version_id;
                }
                else {
                    $replay_version_id = $replay_versions_by_name[$replay_properties->version];
                }
                
                
                /* ---------- Seeds ---------- */
                
                $seed_id = NULL;
                
                if(!isset($seeds_by_name[$replay_properties->seed])) {
                    $seed_id = Seeds::getNewRecordId($leaderboard_source);
                    
                    $seeds_insert_queue->addRecord([
                        'id' => $seed_id,
                        'name' => $replay_properties->seed
                    ]);
                    
                    $seeds_by_name[$replay_properties->seed] = $seed_id;
                }
                else {
                    $seed_id = $seeds_by_name[$replay_properties->seed];
                }
                
                
                /* ---------- Steam Replays ---------- */
                
                $replays_insert_queue->addRecord([
                    'player_pb_id' => $replay->player_pb_id,
                    'downloaded' => 1,
                    'invalid' => 0,
                    'run_result_id' => $run_result_id,
                    'replay_version_id' => $replay_version_id,
                    'seed_id' => $seed_id
                ]);
                
                $this->data_manager->compressTempFileToSaved($replay);
                
                $this->data_manager->deleteTempFile($replay);
            }

            /* ---------- Save Supplemental Data ---------- */
            
            $run_results_insert_queue->commit();
            $replay_versions_insert_queue->commit();
            $seeds_insert_queue->commit();
            
            RunResults::saveNewTemp($leaderboard_source);
            ReplayVersions::saveNewTemp($leaderboard_source);
            Seeds::saveNewTemp($leaderboard_source);
        }
        
        
        /* ---------- Invalid Files ---------- */
        
        $invalid_files = $this->data_manager->getInvalidFiles();
        
        if(!empty($invalid_files)) {
            unset($invalid_files);
        
            foreach($this->data_manager->getInvalidFile() as $invalid_file) {
                $replays_insert_queue->addRecord([
                    'player_pb_id' => $invalid_file->player_pb_id,
                    'downloaded' => 0,
                    'invalid' => 1,
                    'run_result_id' => NULL,
                    'replay_version_id' => NULL,
                    'seed_id' => NULL
                ]);
                
                $this->data_manager->deleteInvalidFile($invalid_file);
            }
        }
        
        
        /* ---------- Wrap Up ---------- */
        
        $replays_insert_queue->commit();
        
        Replays::updateDownloadedFromTemp($leaderboard_source);
        
        DB::commit();
    }
}
