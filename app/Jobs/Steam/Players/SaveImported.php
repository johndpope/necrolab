<?php

namespace App\Jobs\Steam\Players;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use App\Components\DataManagers\Steam\Players as PlayersManager;
use App\LeaderboardSources;
use App\Players;

class SaveImported implements ShouldQueue {
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
    
    /**
     * The data manager used to interact with imported player data.
     *
     * @var \App\Components\DataManagers\Steam\Players
     */
    protected $data_manager;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(PlayersManager $data_manager) {
        $this->data_manager = $data_manager;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        $leaderboard_source = $this->data_manager->getLeaderboardSource();
    
        $temp_files = $this->data_manager->getTempFiles();
        
        if(!empty($temp_files)) {
            DB::beginTransaction();
            
            Players::createTemporaryTable($leaderboard_source);
            
            $player_ids_by_external_id = Players::getAllIdsByPlayerId($leaderboard_source);
            
            $players_insert_queue = Players::getTempInsertQueue($leaderboard_source, 10000);

            foreach($this->data_manager->getTempEntry() as $players) {
                if(!empty($players->response->players)) {
                    foreach($players->response->players as $player) {
                        if(!empty($player->steamid)) {
                            if(!isset($player_ids_by_external_id[$player->steamid])) {
                                throw new Exception("Steam user '{$player->steamid}' does not exist in the players table.");
                            }
                        
                            $player_id = $player_ids_by_external_id[$player->steamid];
    
                            $players_insert_queue->addRecord([
                                'updated' => date('Y-m-d H:i:s'),
                                'id' => $player_id,
                                'username' => $player->personaname ?? NULL,
                                'profile_url' => $player->profileurl ?? NULL,
                                'avatar_url' => $player->avatar ?? NULL
                            ]);
                        }
                    }
                }
            }
            
            $players_insert_queue->commit();
            
            Players::updateFromTemp($leaderboard_source);
            
            DB::commit();
            
            $this->data_manager->deleteTemp();
        }
    }
}
