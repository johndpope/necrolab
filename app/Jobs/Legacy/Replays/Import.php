<?php

namespace App\Jobs\Legacy\Replays;

use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use App\Jobs\Traits\WorksWithinDatabaseTransaction;
use App\Components\PostgresCursor;
use App\LeaderboardSources;
use App\Replays;
use App\Leaderboards;
use App\RunResults;
use App\ReplayVersions;
use App\Seeds;

class Import implements ShouldQueue {
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
    
    protected $leaderboard_source;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct() {}
    
    protected function importRunResults(): void {
        RunResults::createTemporaryTable($this->leaderboard_source);
        
        $insert_queue = RunResults::getTempInsertQueue($this->leaderboard_source, 5000);
        
        $legacy_cursor = new PostgresCursor('legacy_run_results', RunResults::getLegacyImportQuery(), 5000);
        
        $legacy_cursor->setConnection('legacy');

        foreach($legacy_cursor->getRecord() as $legacy_record) {
            $insert_queue->addRecord([
                'id' => $legacy_record->run_result_id,
                'name' => $legacy_record->name,
                'is_win' => $legacy_record->is_win
            ]);
        }
        
        $insert_queue->commit();
        
        RunResults::saveNewTemp($this->leaderboard_source);
        
        RunResults::syncManualSequence($this->leaderboard_source);
    }
    
    protected function importReplayVersions(): void {
        ReplayVersions::createTemporaryTable($this->leaderboard_source);
        
        $insert_queue = ReplayVersions::getTempInsertQueue($this->leaderboard_source, 5000);
        
        $legacy_cursor = new PostgresCursor('legacy_replay_versions', ReplayVersions::getLegacyImportQuery(), 5000);
        
        $legacy_cursor->setConnection('legacy');

        foreach($legacy_cursor->getRecord() as $legacy_record) {
            $insert_queue->addRecord([
                'id' => $legacy_record->steam_replay_version_id,
                'name' => $legacy_record->name
            ]);
        }
        
        $insert_queue->commit();
        
        ReplayVersions::saveNewTemp($this->leaderboard_source);
        
        ReplayVersions::syncManualSequence($this->leaderboard_source);
    }
    
    protected function importReplays(): void {
        $leaderboard_ids_by_external_id = Leaderboards::getIdsByExternalId($this->leaderboard_source);
        $run_results_by_name = RunResults::getAllByName($this->leaderboard_source);
        $seeds_by_name = [];
    
        Replays::createTemporaryTable($this->leaderboard_source);
        Seeds::createTemporaryTable($this->leaderboard_source);
        
        $insert_queue = Replays::getTempInsertQueue($this->leaderboard_source, 6000);
        $seeds_insert_queue = Seeds::getTempInsertQueue($this->leaderboard_source, 20000);
        
        $legacy_cursor = new PostgresCursor('legacy_replays', Replays::getLegacyImportQuery(), 10000);
        
        $legacy_cursor->setConnection('legacy');

        foreach($legacy_cursor->getRecord() as $legacy_record) {
            if(isset($leaderboard_ids_by_external_id[$legacy_record->lbid])) {
                $seed_id = NULL;
                $seed = (string)$legacy_record->seed;
                
                if(!isset($seeds_by_name[$seed])) {
                    $seed_id = Seeds::getNewRecordId($this->leaderboard_source);
                    
                    $seeds_insert_queue->addRecord([
                        'id' => $seed_id,
                        'name' => $seed
                    ]);
                    
                    $seeds_by_name[$seed] = $seed_id;
                }
                else {
                    $seed_id = $seeds_by_name[$seed];
                }
                
                $run_result_id = $run_results_by_name[$legacy_record->run_result]->id;

                $insert_queue->addRecord([
                    'seed_id' => $seed_id,
                    'player_pb_id' => $legacy_record->steam_user_pb_id,
                    'player_id' => $legacy_record->steam_user_id,
                    'run_result_id' => $run_result_id,
                    'replay_version_id' => $legacy_record->steam_replay_version_id,
                    'downloaded' => $legacy_record->downloaded,
                    'invalid' => $legacy_record->invalid,
                    'uploaded_to_s3' => $legacy_record->uploaded_to_s3,
                    'external_id' => $legacy_record->ugcid
                ]);
            }
        }
        
        $seeds_insert_queue->commit();
        $insert_queue->commit();
        
        Seeds::saveNewTemp($this->leaderboard_source);
        Replays::saveLegacyTemp($this->leaderboard_source);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    protected function handleDatabaseTransaction(): void {
        $this->leaderboard_source = LeaderboardSources::getByName('steam');
    
        DB::beginTransaction();
        DB::connection('legacy')->beginTransaction();
        
        $this->importRunResults();
        
        $this->importReplayVersions();
        
        $this->importReplays();
        
        DB::connection('legacy')->commit();
        DB::commit();
    }
}
