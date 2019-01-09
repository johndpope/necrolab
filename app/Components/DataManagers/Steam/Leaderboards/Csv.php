<?php

namespace App\Components\DataManagers\Steam\Leaderboards;

use DateTime;
use ZipArchive;
use stdClass;
use Illuminate\Support\Facades\Storage;
use App\Components\DataManagers\Leaderboards as LeaderboardsManager;
use App\LeaderboardSources;

class Csv
extends LeaderboardsManager {        
    protected $names_path;
    
    protected $temp_names_path;
    
    public static function runClientDownloader(string $executable_path, string $appid, string $username, string $password, string $names_path) {        
        $save_path = dirname($names_path);
        
        exec("cd {$save_path} && /usr/bin/mono {$executable_path} {$username} {$password} {$appid} {$names_path}");
        
        unset($password);
    }
    
    public function __construct(DateTime $date) {
        $leaderboard_source = LeaderboardSources::where('name', 'steam')->first();
    
        parent::__construct($leaderboard_source, 'csv', $date);
        
        $this->names_path = "{$this->getSavedBasePath()}/leaderboards.txt";
        
        $this->temp_names_path = "{$this->getTempBasePath()}/leaderboards.txt";
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
    
    protected function loadTempFiles() {
        $this->temp_files = [];
    
        $all_temp_files = $this->file_storage_engine->allFiles($this->getTempBasePath());
    
        if(!empty($all_temp_files)) {
            foreach($all_temp_files as $temp_file) {
                
                $file_name = basename($temp_file);
                $file_name_split = explode('.', $file_name);
                
                $leaderboard_id = NULL;
                
                if($file_name_split[0] == 'leaderboards') {
                    $leaderboard_id = 'leaderboards';
                }
                else {
                    $leaderboard_id = (int)$file_name_split[0];
                }
                    
                $this->temp_files[$leaderboard_id] = [
                    'path' => $temp_file,
                    'full_path' => "{$this->storage_path}/{$temp_file}"
                ];
            }
        }
    }
    
    public function getTempLeaderboard() {    
        $temp_files = $this->getTempFiles();
        
        if(!empty($temp_files)) {
            foreach($temp_files as $leaderboard_id => $temp_file) {    
                if($leaderboard_id != 'leaderboards') {
                    $file_handle = fopen($temp_file['full_path'], 'r');
                    
                    $leaderboard_name_row = fgetcsv($file_handle);

                    $leaderboard_name = $leaderboard_name_row[0];
                    
                    fclose($file_handle);
                    
                    $leaderboard = new stdClass();
                    
                    $leaderboard->leaderboard_id = $leaderboard_id;
                    $leaderboard->name = $leaderboard_name;
                    $leaderboard->display_name = NULL;
                    $leaderboard->url = NULL;
                    
                    yield $leaderboard;
                }
            }
        }
    }
    
    public function getTempEntry($leaderboard_id) {
        $temp_files = $this->getTempFiles();
        
        if(!empty($temp_files[$leaderboard_id])) {
            $file_handle = fopen($temp_files[$leaderboard_id]['full_path'], 'r');
            
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
        
            foreach($temp_files as $leaderboard_id => $temp_file) {
                $full_path = $temp_file['full_path'];
            
                $relative_path = basename($full_path);
                
                $saved_zip_archive->addFile($full_path, $relative_path);
            }
            
            $saved_zip_archive->close();
        }
    }
    
    public function getTempFile() {}
}
