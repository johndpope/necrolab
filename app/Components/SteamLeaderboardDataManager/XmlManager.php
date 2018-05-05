<?php

namespace App\Components\SteamLeaderboardDataManager;

use DateTime;
use ZipArchive;
use stdClass;
use Illuminate\Support\Facades\Storage;

class XmlManager
extends Core {    
    protected $leaderboards_path;
    
    protected $temp_leaderboards_path;
    
    public static function getParsedXml($unparsed_xml) {
        //Strip any non UTF-8 character from the document that causes the XML to break.
        $leaderboards_xml = preg_replace('/[^[:print:]]/', '', $unparsed_xml);
        
        unset($unparsed_xml);
        
        return simplexml_load_string($leaderboards_xml);
    }
    
    public function __construct(DateTime $date) {
        $this->file_extension = 'xml';
        $this->base_path = 'leaderboard_xml';
    
        parent::__construct($date);
        
        $this->leaderboards_path = "{$this->saved_base_path}/leaderboards.xml";
        
        $this->temp_leaderboards_path = "{$this->temp_base_path}/leaderboards.xml";
    }
    
    public function getLeaderboardsPath() {        
        return "{$this->storage_path}/{$this->leaderboards_path}";
    }
    
    public function getTempLeaderboardsPath() {                
        return "{$this->storage_path}/{$this->temp_leaderboards_path}";
    }
    
    public function saveTempLeaderboards($xml) {        
        Storage::disk('local')->put($this->temp_leaderboards_path, $xml);
    }
    
    public function saveTempEntries($lbid, $page_number, $xml) {        
        Storage::disk('local')->put("{$this->temp_base_path}/{$lbid}/page_{$page_number}.xml", $xml);
    }
    
    public function getTempFiles() {
        if(empty($this->temp_files)) {
            $all_temp_files = Storage::disk('local')->allFiles($this->temp_base_path);

            if(!empty($all_temp_files)) {
                foreach($all_temp_files as $temp_file) {
                    $file_name = basename($temp_file);
                    $file_name_split = explode('.', $file_name);
                    
                    $lbid = NULL;
                    $page_number = 0;
                    
                    $temp_file_entry = [
                        'path' => $temp_file,
                        'full_path' => "{$this->storage_path}/{$temp_file}"
                    ];
                    
                    if($file_name_split[0] == 'leaderboards') {
                        $this->temp_files['leaderboards'] = $temp_file_entry;
                    }
                    else {
                        $lbid = (int)basename(dirname($temp_file));
                        $page_number = str_replace('page_', '', $file_name_split[0]);
                        
                        $this->temp_files[$lbid][$page_number] = $temp_file_entry;
                    }
                }
            }
        }

        return $this->temp_files;
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
                    
                    $leaderboard->lbid = (string)$leaderboard_xml->lbid;
                    $leaderboard->name = (string)$leaderboard_xml->name;
                    $leaderboard->display_name = (string)$leaderboard_xml->display_name;
                    $leaderboard->url = (string)$leaderboard_xml->url;
                    
                    yield $leaderboard;
                }
            }
        }
    }
    
    public function getTempEntry($lbid) {
        $temp_files = $this->getTempFiles();
        
        if(!empty($temp_files[$lbid])) {
            $entry_pages = $temp_files[$lbid];
        
            foreach($entry_pages as $entry_file) {
                $entries_xml_string = file_get_contents($entry_file['full_path']);
                
                $entries_xml = static::getParsedXml($entries_xml_string);
                
                unset($entries_xml_string);
                
                if(!empty($entries_xml->entries->entry)) {
                    $xml_entries = $entries_xml->entries->entry;
                    
                    foreach($xml_entries as $xml_entry) {                    
                        $entry = new stdClass();
                        
                        $entry->steamid = (string)$xml_entry->steamid;
                        $entry->score = (string)$xml_entry->score;
                        $entry->ugcid = (string)$xml_entry->ugcid;
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
        
            foreach($temp_files as $lbid => $temp_file_group) {
                if($lbid == 'leaderboards') {
                    $temp_file = $temp_file_group;
                
                    $full_path = $temp_file['full_path'];
                
                    $saved_zip_archive->addFile($full_path, basename($full_path));
                }
                else {
                    foreach($temp_file_group as $temp_file) {
                        $full_path = $temp_file['full_path'];
                        
                        $relative_path = "{$lbid}/" . basename($full_path);
                        
                        $saved_zip_archive->addFile($full_path, $relative_path);
                    }
                }
            }
            
            $saved_zip_archive->close();
        }
    }
    
    public function getUrls() {
        $start_date = new DateTime('2017-01-01');
        $end_date = new DateTime(date('Y-m-d'));
        
        $current_date = clone $start_date;
        
        $xml_urls = array();
        
        while($current_date <= $end_date) {
            $xml_urls[] = "https://necrolab.s3.amazonaws.com/leaderboard_xml/{$current_date->format('Y-m-d')}.zip";
        
            $current_date->add(new DateInterval('P1D'));
        }
        
        return $xml_urls;
    }
}