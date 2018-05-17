<?php

namespace App\Components\SteamDataManager;

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
    
    protected $temp_files = [];

    public function __construct(DateTime $date) {
        $this->date = $date;
        
        $date_formatted = $date->format('Y-m-d');
        
        $this->storage_path = rtrim(Storage::disk('local')->getAdapter()->getPathPrefix(), '/\\');
        
        $this->saved_base_path = "{$this->base_path}/{$date_formatted}";
        
        $this->temp_base_path = "{$this->base_path}/temp/{$date_formatted}";
        
        $this->s3_base_path = "{$this->base_path}/s3_queue/{$date_formatted}";
    }
    
    public function getDate() {
        return $this->date;
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
    
    public function getTempFiles() {}
    
    public function compressTempToSaved() {}
    
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
            Storage::disk('s3')->put("{$saved_file_path}", $local_storage->get($saved_file_path));
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
    
    public function getTempFile() {}
}