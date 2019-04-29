<?php

namespace App\Jobs\Legacy\Leaderboards;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use App\Components\PostgresCursor;
use App\Leaderboards;
use App\LeaderboardRankingTypes;
use App\LeaderboardSnapshots;
use App\LeaderboardEntryDetails;
use App\Dates;
use App\LeaderboardSources;
use App\LeaderboardTypes;
use App\Characters;
use App\Releases;
use App\Modes;
use App\SeededTypes;
use App\MultiplayerTypes;
use App\Soundtracks;
use App\RankingTypes;

class Import implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
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
    
    protected $leaderboard_source = [];
    
    protected $dates;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct() {}
    
    protected function importLeaderboards(): void {
        $leaderboard_types = LeaderboardTypes::getAllByName();
        $characters = Characters::getAllByName();
        $releases = Releases::getAllByName();
        $modes = Modes::getAllByName();
        $seeded_types = SeededTypes::getAllByName();
        $multiplayer_types = MultiplayerTypes::getAllByName();
        $soundtracks = Soundtracks::getAllByName();
        $ranking_types = RankingTypes::getAllByName();
    
        Leaderboards::createTemporaryTable($this->leaderboard_source);
        LeaderboardRankingTypes::createTemporaryTable($this->leaderboard_source);
        
        $leaderboards_insert_queue = Leaderboards::getTempInsertQueue($this->leaderboard_source, 5000);
        $leaderboard_ranking_types_insert_queue = LeaderboardRankingTypes::getTempInsertQueue($this->leaderboard_source, 10000);
        
        $legacy_cursor = new PostgresCursor('legacy_leaderboards', Leaderboards::getLegacyImportQuery(), 1000);
        
        $legacy_cursor->setConnection('legacy');
        
        $rejected_leaderboards = [];

        foreach($legacy_cursor->getRecord() as $legacy_record) {
            $leaderboard_type = NULL;
            
            if(!empty($legacy_record->is_deathless)) {
                $leaderboard_type = $leaderboard_types['deathless'];
            }
            elseif(!empty($legacy_record->is_speedrun)) {
                $leaderboard_type = $leaderboard_types['speed'];
            }
            elseif(!empty($legacy_record->is_daily)) {
                $leaderboard_type = $leaderboard_types['daily'];
            }
            else {
                $leaderboard_type = $leaderboard_types['score'];
            }
            
            $character = NULL;
            
            if(isset($characters[$legacy_record->character])) {
                $character = $characters[$legacy_record->character];
            }
            
            $release = NULL;
            
            if(isset($releases[$legacy_record->release])) {
                $release = $releases[$legacy_record->release];
            }
            
            $mode = NULL;
            
            if(isset($modes[$legacy_record->mode])) {
                $mode = $modes[$legacy_record->mode];
            }
            
            $seeded_type = $seeded_types['unseeded'];
            
            if(!empty($legacy_record->is_seeded) || !empty($legacy_record->is_daily)) {
                $seeded_type = $seeded_types['seeded'];
            }
            
            $multiplayer_type = $multiplayer_types['single'];
            
            if(!empty($legacy_record->is_co_op)) {
                $multiplayer_type = $multiplayer_types['co_op'];
            }
            
            $soundtrack = $soundtracks['default'];
            
            if(!empty($legacy_record->is_custom)) {
                $soundtrack = $soundtracks['custom'];
            }
            
            $daily_date_id = NULL;
            
            if(!empty($legacy_record->daily_date) && isset($this->dates[$legacy_record->daily_date])) {
                $daily_date_id = $this->dates[$legacy_record->daily_date]->id;
            }
            
            if(empty($legacy_record->is_daily) || (!empty($legacy_record->is_daily) && !empty($daily_date_id))) {
                $leaderboards_insert_queue->addRecord([
                    'id' => $legacy_record->leaderboard_id,
                    'leaderboard_type_id' => $leaderboard_type->id,
                    'character_id' => $character->id,
                    'release_id' => $release->id,
                    'mode_id' => $mode->id,
                    'seeded_type_id' => $seeded_type->id,
                    'multiplayer_type_id' => $multiplayer_type->id,
                    'soundtrack_id' => $soundtrack->id,
                    'daily_date_id' => $daily_date_id,
                    'external_id' => $legacy_record->lbid,
                    'name' => $legacy_record->name,
                    'display_name' => $legacy_record->display_name,
                    'url' => $legacy_record->url
                ]);
                
                if(!empty($legacy_record->is_power_ranking)) {
                    $leaderboard_ranking_types_insert_queue->addRecord([
                        'leaderboard_id' => $legacy_record->leaderboard_id,
                        'ranking_type_id' => $ranking_types['power']->id
                    ]);
                }
                
                if(!empty($legacy_record->is_daily_ranking)) {
                    $leaderboard_ranking_types_insert_queue->addRecord([
                        'leaderboard_id' => $legacy_record->leaderboard_id,
                        'ranking_type_id' => $ranking_types['daily']->id
                    ]);
                }
            }
            else {
                $rejected_leaderboards[] = $legacy_record->leaderboard_id;
            }
        }
        
        $leaderboards_insert_queue->commit();
        $leaderboard_ranking_types_insert_queue->commit();
        
        Leaderboards::saveNewTemp($this->leaderboard_source);
        LeaderboardRankingTypes::saveNewTemp($this->leaderboard_source);
        
        Leaderboards::syncManualSequence($this->leaderboard_source);
    }
    
    protected function importLeaderboardSnapshots(): void {
        LeaderboardSnapshots::createTemporaryTable($this->leaderboard_source);
        
        $insert_queue = LeaderboardSnapshots::getTempInsertQueue($this->leaderboard_source, 10000);
        
        $legacy_cursor = new PostgresCursor('legacy_leaderboard_snapshots', LeaderboardSnapshots::getLegacyImportQuery(), 5000);
        
        $legacy_cursor->setConnection('legacy');

        foreach($legacy_cursor->getRecord() as $legacy_record) {
            $daily_date_id = NULL;
            
            if(!empty($legacy_record->daily_date) && isset($this->dates[$legacy_record->daily_date])) {
                $daily_date_id = $this->dates[$legacy_record->daily_date]->id;
            }
        
            if(empty($legacy_record->is_daily) || (!empty($legacy_record->is_daily) && !empty($daily_date_id))) {
                $insert_queue->addRecord([
                    'created' => $legacy_record->created,
                    'updated' => $legacy_record->updated,
                    'id' => $legacy_record->leaderboard_snapshot_id,
                    'leaderboard_id' => $legacy_record->leaderboard_id,
                    'date_id' => $this->dates[$legacy_record->date]->id
                ]);
            }
        }
        
        $insert_queue->commit();
        
        LeaderboardSnapshots::saveNewTemp($this->leaderboard_source);
        
        LeaderboardSnapshots::syncManualSequence($this->leaderboard_source);
    }
    
    protected function importLeaderboardEntryDetails(): void {
        LeaderboardEntryDetails::createTemporaryTable($this->leaderboard_source);
        
        $insert_queue = LeaderboardEntryDetails::getTempInsertQueue($this->leaderboard_source, 5000);
        
        $legacy_cursor = new PostgresCursor('legacy_leaderboard_entry_details', LeaderboardEntryDetails::getLegacyImportQuery(), 5000);
        
        $legacy_cursor->setConnection('legacy');

        foreach($legacy_cursor->getRecord() as $legacy_record) {
            $insert_queue->addRecord([
                'id' => $legacy_record->leaderboard_entry_details_id,
                'name' => $legacy_record->details
            ]);
        }
        
        $insert_queue->commit();
        
        LeaderboardEntryDetails::saveNewTemp($this->leaderboard_source);
        
        LeaderboardEntryDetails::syncManualSequence($this->leaderboard_source);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        $this->leaderboard_source = LeaderboardSources::getByName('steam');
        $this->dates = Dates::getAllByName();
    
        DB::beginTransaction();
        DB::connection('legacy')->beginTransaction();
        
        $this->importLeaderboards();
        
        $this->importLeaderboardSnapshots();
        
        $this->importLeaderboardEntryDetails();
        
        DB::connection('legacy')->commit();
        DB::commit();
    }
}
