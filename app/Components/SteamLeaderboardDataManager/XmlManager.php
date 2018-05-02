<?php

namespace App\Components\SteamLeaderboardDataManager;

use DateTime;
use ZipArchive;
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
        $all_temp_files = Storage::disk('local')->allFiles($this->temp_base_path);
        
        $temp_files = [];

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
                    $temp_files['leaderboards'] = $temp_file_entry;
                }
                else {
                    $lbid = (int)basename(dirname($temp_file));
                    $page_number = str_replace('page_', '', $file_name_split[0]);
                    
                    $temp_files[$lbid][$page_number] = $temp_file_entry;
                }
            }
        }

        return $temp_files;
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