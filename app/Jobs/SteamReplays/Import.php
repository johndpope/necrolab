<?php

namespace App\Jobs\SteamReplays;

use Exception;
use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use GuzzleHttp\Client as GuzzleClient;
use Steam\Configuration as SteamApiConfiguration;
use Steam\Runner\GuzzleRunner;
use Steam\Runner\DecodeJsonStringRunner;
use Steam\Steam as SteamApi;
use Steam\Utility\GuzzleUrlBuilder;
use Steam\Command\RemoteStorage\GetUGCFileDetails;
use App\Components\CallbackHandler;
use App\Components\PostgresCursor;
use App\Components\SteamDataManager\Replays as SteamReplaysManager;
use App\SteamReplays;
use App\Jobs\SteamReplays\SaveImported as SaveImportedJob;

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
        
        $application_id = env('STEAM_APPID');
        
        $guzzle_client = new GuzzleClient();
        
        /* ---------- Configure Data Manager ---------- */ 
        
        $this->data_manager = new SteamReplaysManager();
        
        $this->data_manager->deleteTemp();
        
        
        /* ---------- Configure Steam API ---------- */ 
        
        $this->steam_api = new SteamApi(new SteamApiConfiguration([
            SteamApiConfiguration::STEAM_KEY => env('STEAM_API_KEY')
        ]));
        
        $this->steam_api->addRunner(new GuzzleRunner(clone $guzzle_client, new GuzzleUrlBuilder()));
        
        $this->steam_api->addRunner(new DecodeJsonStringRunner());
        
        
        /* ---------- Configure Callback handler to request UGC file details ---------- */
        
        $ugc_callback_handler = new CallbackHandler();
        
        $ugc_callback_handler->setCallback([
            $this->steam_api,
            'run'
        ]);
        
        $ugc_callback_handler->setReattempts(5);
        
        $ugc_callback_handler->setReattemptInterval(1);
        
        
        /* ---------- Configure Callback handler to download replay data ---------- */
        
        $download_callback_handler = new CallbackHandler();
        
        $download_callback_handler->setCallback([
            $guzzle_client,
            'request'
        ]);
        
        $download_callback_handler->setReattempts(5);
        
        $download_callback_handler->setReattemptInterval(1);
        
        
        /* ---------- Retrieve outdated record ids and add to record queue ---------- */ 
        
        $cursor = new PostgresCursor(
            'get_unsaved_replays', 
            SteamReplays::getUnsavedQuery(),
            3000
        );
        
        foreach($cursor->getRecord() as $unsaved_record) {
            $file_data = NULL;
        
            if($unsaved_record->ugcid != '-1') {
                try {
                    $ugc_callback_handler->setArguments([
                        new GetUGCFileDetails($application_id, $unsaved_record->ugcid)
                    ]);
                
                    $ugc_meta_data = $ugc_callback_handler->execute();
                    
                    $file_url = $ugc_meta_data['data']['url'] ?? NULL;
                    
                    if(!empty($file_url)) {
                        $download_callback_handler->setArguments([
                            'GET',
                            $file_url
                        ]);
                        
                        $download_response = $download_callback_handler->execute();
                        
                        $file_data = $download_response->getBody()->getContents();
                    }
                }
                catch(Exception $exception) {
                    $file_data = NULL;
                }
            }

            if(!empty($file_data)) {            
                $this->data_manager->saveTempFile($unsaved_record->steam_user_pb_id, (string)$unsaved_record->ugcid, $file_data);
            }
            else {
                $this->data_manager->saveInvalidFile($unsaved_record->steam_user_pb_id);
            }
        }
        
        SaveImportedJob::dispatch();
    }
}