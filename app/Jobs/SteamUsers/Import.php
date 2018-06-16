<?php

namespace App\Jobs\SteamUsers;

use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use GuzzleHttp\Client;
use Steam\Configuration as SteamApiConfiguration;
use Steam\Runner\GuzzleRunner;
use Steam\Steam as SteamApi;
use Steam\Utility\GuzzleUrlBuilder;
use Steam\Command\User\GetPlayerSummaries;
use App\Components\RecordQueue;
use App\Components\CallbackHandler;
use App\Components\SteamDataManager\SteamUsers as SteamUsersManager;
use App\Components\PostgresCursor;
use App\SteamUsers;
use App\Jobs\SteamUsers\SaveImported as SaveImportedJob;

class Import implements ShouldQueue {
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
        
        
        /* ---------- Configure Data Manager ---------- */ 
        
        $this->data_manager = new SteamUsersManager($this->date);
        
        $this->data_manager->deleteTemp();
        
        
        /* ---------- Configure Steam API ---------- */ 
        
        $this->steam_api = new SteamApi(new SteamApiConfiguration([
            SteamApiConfiguration::STEAM_KEY => env('STEAM_API_KEY')
        ]));
        
        $this->steam_api->addRunner(new GuzzleRunner(new Client(), new GuzzleUrlBuilder()));
        
        
        /* ---------- Configure Callback Handler ---------- */ 
        
        $callback_handler = new CallbackHandler();
        
        $callback_handler->setCallback(function(array $steamid_rows) {
            $steamids = [];
        
            if(!empty($steamid_rows)) {
                foreach($steamid_rows as $steamid_row) {
                    $steamids[] = $steamid_row[0];
                }
            }
        
            $response = $this->steam_api->run(new GetPlayerSummaries($steamids));
            
            $steam_profile_data = mb_convert_encoding($response->getBody()->getContents(), 'UTF-8', 'auto');
            
            $this->data_manager->saveTempFile($this->group_number, $steam_profile_data);
            
            $this->group_number += 1;
        });
        
        $callback_handler->setReattempts(5);
        
        $callback_handler->setReattemptInterval(1);
        
        
        /* ---------- Configure Record Queue ---------- */ 
        
        $record_queue = new RecordQueue(100);
        
        $record_queue->addCommitCallback($callback_handler);
        
        
        /* ---------- Retrieve outdated record ids and add to record queue ---------- */ 
        
        $cursor = new PostgresCursor(
            'get_outdated_steam_users', 
            SteamUsers::getOutdatedIdsQuery(),
            20000
        );
        
        foreach($cursor->getRecord() as $outdated_record) {
            $record_queue->addRecord([$outdated_record->steamid]);
        }
        
        SaveImportedJob::dispatch();
    }
}