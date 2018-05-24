<?php

namespace App\Components\SteamDataManager\Leaderboards;

use DateTime;
use ZipArchive;
use stdClass;
use Illuminate\Support\Facades\Storage;
use App\Components\SteamDataManager\Core;

class Csv
extends Core {        
    protected $names_path;
    
    protected $temp_names_path;
    
    public function __construct(DateTime $date) {
        $this->file_extension = 'csv';
        $this->base_path = 'leaderboard_csv';
    
        parent::__construct($date);
        
        $this->names_path = "{$this->saved_base_path}/leaderboards.txt";
        
        $this->temp_names_path = "{$this->temp_base_path}/leaderboards.txt";
    }
    
    public function getNamesPath() {        
        return "{$this->storage_path}/{$this->names_path}";
    }
    
    public function getTempNamesPath() {                
        return "{$this->storage_path}/{$this->temp_names_path}";
    }
    
    public function saveTempNames(array $names) {        
        $this->file_storage_engine->put($this->temp_names_path, implode("\n", $names));
    }
    
    public function getTempFiles() {
        if(empty($this->temp_files)) {
            $all_temp_files = $this->file_storage_engine->allFiles($this->temp_base_path);
        
            if(!empty($all_temp_files)) {
                foreach($all_temp_files as $temp_file) {
                    
                    $file_name = basename($temp_file);
                    $file_name_split = explode('.', $file_name);
                    
                    $lbid = NULL;
                    
                    if($file_name_split[0] == 'leaderboards') {
                        $lbid = 'leaderboards';
                    }
                    else {
                        $lbid = (int)$file_name_split[0];
                    }
                        
                    $this->temp_files[$lbid] = [
                        'path' => $temp_file,
                        'full_path' => "{$this->storage_path}/{$temp_file}"
                    ];
                }
            }
        }

        return $this->temp_files;
    }
    
    public function getTempLeaderboard() {    
        $temp_files = $this->getTempFiles();
        
        if(!empty($temp_files)) {
            foreach($temp_files as $lbid => $temp_file) {    
                if($lbid != 'leaderboards') {
                    $file_handle = fopen($temp_file['full_path'], 'r');
                    
                    $leaderboard_name_row = fgetcsv($file_handle);

                    $leaderboard_name = $leaderboard_name_row[0];
                    
                    fclose($file_handle);
                    
                    $leaderboard = new stdClass();
                    
                    $leaderboard->lbid = $lbid;
                    $leaderboard->name = $leaderboard_name;
                    $leaderboard->display_name = NULL;
                    $leaderboard->url = NULL;
                    
                    yield $leaderboard;
                }
            }
        }
    }
    
    public function getTempEntry($lbid) {
        $temp_files = $this->getTempFiles();
        
        if(!empty($temp_files[$lbid])) {
            $file_handle = fopen($temp_files[$lbid]['full_path'], 'r');
            
            //Discard the first row since it only contains the leaderboard name
            fgetcsv($file_handle);
            
            while($entry_row = fgetcsv($file_handle)) {
                $entry = new stdClass();
                        
                $entry->steamid = $entry_row[0];
                $entry->score = (int)$entry_row[2];
                $entry->ugcid = $entry_row[3];
                $entry->zone = (int)$entry_row[4];
                $entry->level = (int)$entry_row[5];
                $entry->details = '';
                
                yield $entry;
            }
            
            fclose($file_handle);
        }
    }
    
    public function compressTempToSaved() {
        $temp_files = $this->getTempFiles();
        
        if(!empty($temp_files)) {
            $saved_zip_archive = new ZipArchive();

            $saved_zip_archive->open("{$this->getFullSavedBasePath()}.zip", ZipArchive::CREATE | ZipArchive::OVERWRITE);
        
            foreach($temp_files as $lbid => $temp_file) {
                $full_path = $temp_file['full_path'];
            
                $relative_path = basename($full_path);
                
                $saved_zip_archive->addFile($full_path, $relative_path);
            }
            
            $saved_zip_archive->close();
        }
    }
    
    public static function runClientDownloader(string $executable_path, string $appid, string $username, string $password, string $names_path) {        
        $save_path = dirname($names_path);
        
        exec("cd {$save_path} && /usr/bin/mono {$executable_path} {$username} {$password} {$appid} {$names_path}");
        
        unset($password);
    }
}