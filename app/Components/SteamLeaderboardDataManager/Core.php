<?php

namespace App\Components\SteamLeaderboardDataManager;

use DateTime;
use ZipArchive;
use Illuminate\Support\Facades\Storage;

class Core {
    protected $date;
    
    protected $file_extension;
    
    protected $storage_path;
    
    protected $base_path;
    
    protected $saved_base_path;
    
    protected $temp_base_path;
    
    protected $s3_base_path;

    public function __construct(DateTime $date) {
        $this->date = $date;
        
        $date_formatted = $date->format('Y-m-d');
        
        $this->storage_path = rtrim(Storage::disk('local')->getAdapter()->getPathPrefix(), '/\\');
        
        $this->saved_base_path = "{$this->base_path}/{$date_formatted}";
        
        $this->temp_base_path = "{$this->base_path}/temp/{$date_formatted}";
        
        $this->s3_base_path = "{$this->base_path}/s3_queue/{$date_formatted}";
    }
    
    public function getSavedBasePath() {
        return $this->saved_base_path;
    }
    
    public function getFullSavedBasePath() {
        return "{$this->storage_path}/{$this->saved_base_path}";
    }
    
    public function getSavedContents() {
        return Storage::disk('local')->get($this->saved_base_path);
    }
    
    public function getTempBasePath() {        
        return "{$this->storage_path}/{$this->temp_base_path}";
    }
    
    public function getS3BasePath() {                
        return "{$this->storage_path}/{$this->s3_base_path}";
    }
    
    public function deleteTemp() {
        Storage::disk('local')->deleteDirectory($this->temp_base_path);
    }
    
    public function getTempFiles() {
        $all_temp_files = Storage::disk('local')->allFiles($this->temp_base_path);
        
        $temp_files = [];
        
        if(!empty($all_temp_files)) {
            foreach($all_temp_files as $temp_file) {
                
                $file_name = basename($temp_file);
                $file_name_split = explode('.', $file_name);
                
                $lbid = NULL;
                
                if($file_name == 'leaderboards.txt') {
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
        
            foreach($temp_files as $temp_file) {
                $full_path = $temp_file['full_path'];
            
                $relative_path = basename($full_path);
                
                $saved_zip_archive->addFile($full_path, $relative_path);
            }
            
            $saved_zip_archive->close();
        }
    }
    
    public function decompressToTemp() {
        $saved_zip_archive = new ZipArchive();
        
        if($saved_zip_archive->open("{$this->getFullSavedBasePath()}.zip") === true) {
            $saved_zip_archive->extractTo($this->getTempBasePath());
            
            $saved_zip_archive->close();
        }
    }

    public function copySavedToS3() {        
        $saved_file_path = "{$this->saved_base_path}.zip";
        
        $local_storage = Storage::disk('local');
        
        if($local_storage->exists($saved_file_path)) {
            Storage::disk('s3')->put("temp/{$saved_file_path}", $local_storage->get($saved_file_path));
        }
    }
    
    public function getUrls() {
        $start_date = new DateTime('2017-01-01');
        $end_date = new DateTime(date('Y-m-d'));
        
        $current_date = clone $start_date;
        
        $csv_urls = [];
        
        while($current_date <= $end_date) {
            $csv_urls[] = env('AWS_URL') . "/leaderboard_{$this->file_extension}/{$this->date->format('Y-m-d')}.zip";
        
            $current_date->add(new DateInterval('P1D'));
        }
        
        return $csv_urls;
    }
}