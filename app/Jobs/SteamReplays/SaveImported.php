<?php

namespace App\Jobs\SteamReplays;

use DateTime;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use App\Components\SteamDataManager\Replays as SteamReplaysManager;
use App\SteamReplays;
use App\RunResults;
use App\SteamReplayVersions;
use App\Seeds;

class SaveImported implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;
    
    protected $date;
    
    protected $data_manager;
    
    protected $steam_api;
    
    protected $group_number = 1;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {        
        $this->data_manager = new SteamReplaysManager();
        
        DB::beginTransaction();
        
        SteamReplays::createTemporaryTable();
        
        $steam_replays_insert_queue = SteamReplays::getTempInsertQueue(10000);
        
        $temp_files = $this->data_manager->getTempFiles();
        
        if(!empty($temp_files)) { 
            unset($temp_files);
        
            $run_results_by_name = RunResults::getAllIdsByName();
            $replay_versions_by_name = SteamReplayVersions::getAllIdsByName();
            $seeds_by_name = Seeds::getAllIdsByName();
            
            RunResults::createTemporaryTable();
            SteamReplayVersions::createTemporaryTable();
            Seeds::createTemporaryTable();
            
            $run_results_insert_queue = RunResults::getTempInsertQueue(20000);
            $steam_replay_versions_insert_queue = SteamReplayVersions::getTempInsertQueue(30000);
            $seeds_insert_queue = Seeds::getTempInsertQueue(30000);
            

            foreach($this->data_manager->getTempFile() as $steam_replay) {
                $replay_properties = SteamReplays::getParsedReplayProperties($steam_replay->contents);
                
                
                /* ---------- Run Results ---------- */
                
                $run_result_id = NULL;
                
                if(!isset($run_results_by_name[$replay_properties->run_result])) {
                    $run_result_id = RunResults::getNewRecordId();
                    
                    $run_results_insert_queue->addRecord([
                        'run_result_id' => $run_result_id,
                        'name' => $replay_properties->run_result,
                        'is_win' => $replay_properties->is_win
                    ]);
                    
                    $run_results_by_name[$replay_properties->run_result] = $run_result_id;
                }
                else {
                    $run_result_id = $run_results_by_name[$replay_properties->run_result];
                }
                
                
                /* ---------- Steam Replay Versions ---------- */
                
                $steam_replay_version_id = NULL;
                
                if(!isset($replay_versions_by_name[$replay_properties->version])) {
                    $steam_replay_version_id = SteamReplayVersions::getNewRecordId();
                    
                    $steam_replay_versions_insert_queue->addRecord([
                        'steam_replay_version_id' => $steam_replay_version_id,
                        'name' => $replay_properties->version,
                    ]);
                    
                    $replay_versions_by_name[$replay_properties->version] = $steam_replay_version_id;
                }
                else {
                    $steam_replay_version_id = $replay_versions_by_name[$replay_properties->version];
                }
                
                
                /* ---------- Seeds ---------- */
                
                $seed_id = NULL;
                
                if(!isset($seeds_by_name[$replay_properties->seed])) {
                    $seed_id = Seeds::getNewRecordId();
                    
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
                
                $steam_replays_insert_queue->addRecord([
                    'steam_user_pb_id' => $steam_replay->steam_user_pb_id,
                    'downloaded' => 1,
                    'invalid' => 0,
                    'run_result_id' => $run_result_id,
                    'steam_replay_version_id' => $steam_replay_version_id,
                    'seed_id' => $seed_id
                ]);
                
                $this->data_manager->compressTempFileToSaved($steam_replay);
                
                $this->data_manager->deleteTempFile($steam_replay);
            }

            /* ---------- Save Supplemental Data ---------- */
            
            $run_results_insert_queue->commit();
            $steam_replay_versions_insert_queue->commit();
            $seeds_insert_queue->commit();
            
            RunResults::saveNewFromTemp();
            SteamReplayVersions::saveNewFromTemp();
            Seeds::saveNewFromTemp();
        }
        
        
        /* ---------- Invalid Files ---------- */
        
        $invalid_files = $this->data_manager->getInvalidFiles();
        
        if(!empty($invalid_files)) {
            unset($invalid_files);
        
            foreach($this->data_manager->getInvalidFile() as $invalid_file) {
                $steam_replays_insert_queue->addRecord([
                    'steam_user_pb_id' => $invalid_file->steam_user_pb_id,
                    'downloaded' => 0,
                    'invalid' => 1,
                    'run_result_id' => NULL,
                    'steam_replay_version_id' => NULL,
                    'seed_id' => NULL
                ]);
                
                $this->data_manager->deleteInvalidFile($invalid_file);
            }
        }
        
        
        /* ---------- Wrap Up ---------- */
        
        $steam_replays_insert_queue->commit();
        
        SteamReplays::updateDownloadedFromTemp();
        
        DB::commit();
    }
}