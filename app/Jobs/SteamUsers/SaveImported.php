<?php

namespace App\Jobs\SteamUsers;

use DateTime;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use App\Components\SteamDataManager\SteamUsers as SteamUsersManager;
use App\SteamUsers;

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
        $this->date = new DateTime();
        
        $this->data_manager = new SteamUsersManager($this->date);
        
        $temp_files = $this->data_manager->getTempFiles();
        
        if(!empty($temp_files)) {
            DB::beginTransaction();
            
            SteamUsers::createTemporaryTable();
            
            $steam_user_ids_by_steamid = SteamUsers::getAllIdsBySteamid();
            
            $steam_users_insert_queue = SteamUsers::getTempInsertQueue(6500);

            foreach($this->data_manager->getTempEntry() as $steam_users) {
                if(!empty($steam_users->response->players)) {
                    foreach($steam_users->response->players as $steam_user) {
                        if(!isset($steam_user_ids_by_steamid[$steam_user->steamid])) {
                            throw new Exception("Steam user '{$steam_user->steamid}' does not exist in the steam_users table.");
                        }
                    
                        $steam_user_id = $steam_user_ids_by_steamid[$steam_user->steamid];
 
                        $steam_users_insert_queue->addRecord([
                            'steam_user_id' => $steam_user_id,
                            'steamid' => $steam_user->steamid,
                            'updated' => date('Y-m-d H:i:s'),
                            'communityvisibilitystate' => $steam_user->communityvisibilitystate ?? NULL,
                            'profilestate' => $steam_user->profilestate ?? NULL,
                            'personaname' => $steam_user->personaname ?? NULL,
                            'profileurl' => $steam_user->profileurl ?? NULL,
                            'avatar' => $steam_user->avatar ?? NULL,
                            'avatarmedium' => $steam_user->avatarmedium ?? NULL,
                            'avatarfull' => $steam_user->avatarfull ?? NULL
                        ]);
                    }
                }
            }
            
            $steam_users_insert_queue->commit();
            
            SteamUsers::updateFromTemp();
            
            DB::commit();
            
            $this->data_manager->deleteTemp();
        }
    }
}