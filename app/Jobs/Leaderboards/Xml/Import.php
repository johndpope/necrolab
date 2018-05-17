<?php

namespace App\Jobs\Leaderboards\Xml;

use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client as GuzzleClient;
use App\Components\SteamDataManager\Leaderboards\Xml as XmlManager;
use App\Components\CallbackHandler;
use App\Jobs\Leaderboards\UploadToS3;
use App\Jobs\Leaderboards\SaveToDatabase;

class Import implements ShouldQueue {
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
        $steam_leaderboard_data_manager = new XmlManager($this->date);
        
        $steam_leaderboard_data_manager->deleteTemp();
        
        $guzzle_client = new GuzzleClient();
        
        $callback_hander = new CallbackHandler();
        
        $callback_hander->setCallbackReattempts(5);
        $callback_hander->setCallbackReattemptInterval(5);
        
        $callback_hander->setCallback([
            $guzzle_client,
            'request'
        ]);
        
        $callback_hander->setCallbackArguments([
            'GET',
            env('STEAM_LEADERBOARD_XML_URL')
        ]);
        
        $leaderboards_xml_result = $callback_hander->execute();
        
        $leaderboards_xml = $leaderboards_xml_result->getBody();
        
        unset($leaderboards_xml_result);
        
        if(!empty($leaderboards_xml)) {
            $steam_leaderboard_data_manager->saveTempLeaderboards($leaderboards_xml);
        
            $leaderboards = XmlManager::getParsedXml($leaderboards_xml);
            
            if(!empty($leaderboards)) {
                unset($leaderboards_xml);
            
                if(!empty($leaderboards->leaderboard)) {
                    foreach($leaderboards->leaderboard as $leaderboard) {
                        $next_page_url = (string)$leaderboard->url;
                        
                        $page = 1;
                        
                        do {
                            $callback_hander->setCallbackArguments([
                                'GET',
                                $next_page_url
                            ]);
                        
                            $entries_result = $callback_hander->execute();
                            
                            $entries_xml = $entries_result->getBody();
                            
                            if(!empty($entries_xml)) {                                
                                $steam_leaderboard_data_manager->saveTempEntries($leaderboard->lbid, $page, $entries_xml);
                                
                                $entries = XmlManager::getParsedXml($entries_xml);
                                
                                unset($entries_xml);
                            
                                if(!empty($entries->nextRequestURL)) {                    
                                    $next_page_url = (string)$entries->nextRequestURL;
                                }
                                else {
                                    $next_page_url = NULL;
                                }
                            }
                            
                            $page += 1;
                        }
                        while(!empty($next_page_url));
                    }
                }
            }
            
            $steam_leaderboard_data_manager->compressTempToSaved();
        
            $steam_leaderboard_data_manager->deleteTemp();
        }
        
        UploadToS3::dispatch($steam_leaderboard_data_manager);
        SaveToDatabase::dispatch($steam_leaderboard_data_manager);
    }
}