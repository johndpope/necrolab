<?php

namespace App\Components\DataManagers\Steam\Leaderboards;

use DateTime;
use ZipArchive;
use stdClass;
use Illuminate\Support\Facades\Storage;
use App\Components\DataManagers\Leaderboards as LeaderboardsManager;
use App\LeaderboardSources;
use App\Dates;

class Xml
extends LeaderboardsManager {    
    protected $leaderboards_path;
    
    protected $temp_leaderboards_path;
    
    public static function getParsedXml($unparsed_xml) {
        //Strip any non UTF-8 character from the document that causes the XML to break.
        $leaderboards_xml = preg_replace('/[^[:print:]]/', '', $unparsed_xml);
        
        unset($unparsed_xml);
        
        return simplexml_load_string($leaderboards_xml);
    }
    
    public function __construct(Dates $date) {
        $leaderboard_source = LeaderboardSources::where('name', 'steam')->firstOrFail();
    
        parent::__construct($leaderboard_source, 'xml', $date);
        
        $this->leaderboards_path = "{$this->getSavedBasePath()}/leaderboards.xml";
        
        $this->temp_leaderboards_path = "{$this->getTempBasePath()}/leaderboards.xml";
    }
    
    public function getLeaderboardsPath() {        
        return "{$this->storage_path}/{$this->leaderboards_path}";
    }
    
    public function getTempLeaderboardsPath() {                
        return "{$this->storage_path}/{$this->temp_leaderboards_path}";
    }
    
    public function saveTempLeaderboards($xml) {        
        $this->file_storage_engine->put($this->temp_leaderboards_path, $xml);
    }
    
    public function saveTempEntries($leaderboard_id, $page_number, $xml) {        
        $this->file_storage_engine->put("{$this->getTempBasePath()}/{$leaderboard_id}/page_{$page_number}.xml", $xml);
    }
    
    protected function loadTempFiles() {
        $this->temp_files = [];
    
        $all_temp_files = $this->file_storage_engine->allFiles($this->getTempBasePath());

        if(!empty($all_temp_files)) {
            foreach($all_temp_files as $temp_file) {
                $file_name = basename($temp_file);
                $file_name_split = explode('.', $file_name);
                
                $leaderboard_id = NULL;
                $page_number = 0;
                
                $temp_file_entry = [
                    'path' => $temp_file,
                    'full_path' => "{$this->storage_path}/{$temp_file}"
                ];
                
                if($file_name_split[0] == 'leaderboards') {
                    $this->temp_files['leaderboards'] = $temp_file_entry;
                }
                else {
                    $leaderboard_id = (int)basename(dirname($temp_file));
                    $page_number = str_replace('page_', '', $file_name_split[0]);
                    
                    $this->temp_files[$leaderboard_id][$page_number] = $temp_file_entry;
                }
            }
        }
    }
    
    public function getTempLeaderboard() {    
        $temp_files = $this->getTempFiles();
        
        if(!empty($temp_files)) {
            $leaderboards_xml_string = file_get_contents($temp_files['leaderboards']['full_path']);
        
            $leaderboards_xml = static::getParsedXml($leaderboards_xml_string);
            
            unset($leaderboards_xml_string);
            
            if(!empty($leaderboards_xml->leaderboard)) {
                foreach($leaderboards_xml->leaderboard as $leaderboard_xml) {                    
                    $leaderboard = new stdClass();
                    
                    $leaderboard->external_id = (string)$leaderboard_xml->lbid;
                    $leaderboard->name = (string)$leaderboard_xml->name;
                    $leaderboard->display_name = (string)$leaderboard_xml->display_name;
                    $leaderboard->url = (string)$leaderboard_xml->url;
                    
                    yield $leaderboard;
                }
            }
        }
    }
    
    public function getTempEntry($leaderboard_id) {
        $temp_files = $this->getTempFiles();
        
        if(!empty($temp_files[$leaderboard_id])) {
            $entry_pages = $temp_files[$leaderboard_id];
        
            foreach($entry_pages as $entry_file) {
                $entries_xml_string = file_get_contents($entry_file['full_path']);
                
                $entries_xml = static::getParsedXml($entries_xml_string);
                
                unset($entries_xml_string);
                
                if(!empty($entries_xml->entries->entry)) {
                    $xml_entries = $entries_xml->entries->entry;
                    
                    foreach($xml_entries as $xml_entry) {                    
                        $entry = new stdClass();
                        
                        $entry->player_external_id = (string)$xml_entry->steamid;
                        $entry->raw_score = (string)$xml_entry->score;
                        $entry->replay_external_id = (string)$xml_entry->ugcid;
                        $entry->zone = NULL;
                        $entry->level = NULL;
                        $entry->details = (string)$xml_entry->details;
                        
                        yield $entry;
                    }
                }
            }
        }
    }
    
    public function compressTempToSaved() {
        $temp_files = $this->getTempFiles();
        
        if(!empty($temp_files)) {
            $saved_zip_archive = new ZipArchive();

            $saved_zip_archive->open("{$this->getFullSavedBasePath()}.zip", ZipArchive::CREATE | ZipArchive::OVERWRITE);
        
            foreach($temp_files as $leaderboard_id => $temp_file_group) {
                if($leaderboard_id == 'leaderboards') {
                    $temp_file = $temp_file_group;
                
                    $full_path = $temp_file['full_path'];
                
                    $saved_zip_archive->addFile($full_path, basename($full_path));
                }
                else {
                    foreach($temp_file_group as $temp_file) {
                        $full_path = $temp_file['full_path'];
                        
                        $relative_path = "{$leaderboard_id}/" . basename($full_path);
                        
                        $saved_zip_archive->addFile($full_path, $relative_path);
                    }
                }
            }
            
            $saved_zip_archive->close();
        }
    }
    
    public function getTempFile() {}
}
