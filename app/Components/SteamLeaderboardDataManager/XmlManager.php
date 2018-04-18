<?php

namespace App\Components\SteamLeaderboardDataManager;

use DateTime;
use Illuminate\Support\Facades\Storage;

class XmlManager
extends Core {    
    protected $leaderboards_path;
    
    protected $temp_leaderboards_path;
    
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
    
    public static function getParsedXml($unparsed_xml) {
        //Strip any non UTF-8 character from the document that causes the XML to break.
        $leaderboards_xml = preg_replace('/[^[:print:]]/', '', $unparsed_xml);
        
        unset($unparsed_xml);
        
        return simplexml_load_string($leaderboards_xml);
    }
    
    public static function getXml($file_path) {    
        return file_get_contents($file_path);
    }
    
    public static function getXmlFiles(DateTime $date, $temp_directory = false) {  
        $snapshot_path = NULL;
        $search_extension = NULL;
        $full_extension = 'xml';
        
        if(empty($temp_directory)) {
            $snapshot_path = static::getXmlPath($date);
            
            $search_extension = 'gz';
            $full_extension .= '.gz';
        }
        else {
            $snapshot_path = static::getXmlTempPath($date);
            
            $search_extension = 'xml';
        }
        
        $xml_file_groups = array();
        
        if(is_dir($snapshot_path)) {
            $directory_iterator = new RecursiveDirectoryIterator($snapshot_path);
            $file_iterator = new RecursiveIteratorIterator($directory_iterator);
            $matched_files = new RegexIterator($file_iterator, "/^.+\.{$search_extension}$/i", RecursiveRegexIterator::GET_MATCH);
            
            foreach($matched_files as $matched_file) {
                if(strpos($matched_file[0], "leaderboards.{$full_extension}") !== false) {
                    $xml_file_groups['leaderboards_xml'] = $matched_file[0];
                }
                else {
                    $file_name = $matched_file[0];
                    $file_name_split = explode('/', $matched_file[0]);
                    
                    $xml_file_name = array_pop($file_name_split);
                    $xml_file_name_split = explode('_', $xml_file_name);
                    
                    $page_number = array_pop($xml_file_name_split);
                    $page_number = (int)str_replace(".{$full_extension}", '', $page_number);
                    
                    $lbid = array_pop($file_name_split);
                    
                    if(empty($xml_file_groups[$lbid])) {
                        $xml_file_groups[$lbid] = array();
                    }
                        
                    $xml_file_groups[$lbid][$page_number] = $matched_file[0];
                }
            }
            
            if(!empty($xml_file_groups)) {
                foreach($xml_file_groups as $lbid => &$xml_files) {
                    if($lbid != 'leaderboards_xml') {
                        ksort($xml_files);
                    }
                }
            }
        }
        
        return $xml_file_groups;
    }
    
    public static function getXmlUrls() {
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