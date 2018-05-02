<?php

namespace App\Components\SteamLeaderboardDataManager;

use DateTime;
use ZipArchive;
use Illuminate\Support\Facades\Storage;

class CsvManager
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
        Storage::disk('local')->put($this->temp_names_path, implode("\n", $names));
    }
    
    public function getTempFiles() {
        $all_temp_files = Storage::disk('local')->allFiles($this->temp_base_path);
        
        $temp_files = [];
        
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
                    
                $temp_files[$lbid] = [
                    'path' => $temp_file,
                    'full_path' => "{$this->storage_path}/{$temp_file}"
                ];
            }
        }

        return $temp_files;
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