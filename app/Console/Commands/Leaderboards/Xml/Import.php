<?php

namespace App\Console\Commands\Leaderboards\Xml;

use DateTime;
use Illuminate\Console\Command;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client as GuzzleClient;
use App\Components\SteamLeaderboardDataManager\XmlManager;
use App\Components\CallbackHandler;
use App\Jobs\Leaderboards\Xml\UploadToS3;
use App\Jobs\Leaderboards\Xml\SaveToDatabase;

class Import extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaderboards:xml:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports leaderboard entries XML data from the Steam web API for the current date.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Imports leaderboard entries XML data for the specified date.
     *
     * @param DateTime $date
     * @return mixed
     */
    protected function importXml(DateTime $date) {
        $steam_leaderboard_data_manager = new XmlManager($date);
        
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
        
        UploadToS3::dispatch($date);
        SaveToDatabase::dispatch($date);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $this->importXml(new DateTime());
    }
}
