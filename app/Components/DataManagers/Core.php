<?php

namespace App\Components\DataManagers;

use DateTime;
use ZipArchive;
use Illuminate\Support\Facades\Storage;
use App\LeaderboardSources;

abstract class Core {
    protected $leaderboard_source;
    
    protected $file_storage_engine;
    
    protected $storage_path;
    
    private $base_path_segments = [];

    public function __construct(LeaderboardSources $leaderboard_source) {
        $this->leaderboard_source = $leaderboard_source;
        
        $this->file_storage_engine = Storage::disk('local');
        
        $this->addBasePathSegment($this->leaderboard_source->name);
        
        $this->storage_path = rtrim($this->file_storage_engine->getAdapter()->getPathPrefix(), '/\\');
    }
    
    public function getLeaderboardSource() {
        return $this->leaderboard_source;
    }
    
    protected function addBasePathSegment(string $segment_name) {
        $this->base_path_segments[] = $segment_name;
    }
    
    public function getBasePath() {
        return implode('/', $this->base_path_segments);
    }
    
    public function getSavedBasePath() {
        return $this->getBasePath();
    }
    
    public function getFullSavedBasePath() {
        return "{$this->storage_path}/{$this->getSavedBasePath()}";
    }
    
    public function getSavedContents() {
        return $this->file_storage_engine->get($this->getSavedBasePath());
    }
    
    public function getTempBasePath() {
        return $this->getBasePath() . '/temp';
    }
    
    public function getFullTempBasePath() {        
        return "{$this->storage_path}/{$this->getTempBasePath()}";
    }
    
    public function deleteTemp() {
        $this->file_storage_engine->deleteDirectory($this->getTempBasePath());
    }
    
    abstract protected function loadTempFiles();
    
    public function getTempFiles() {
        if(empty($this->temp_files)) {
            $this->loadTempFiles();
        }

        return $this->temp_files;
    }
    
    abstract public function compressTempToSaved();
    
    public function decompressToTemp() {
        $saved_zip_archive = new ZipArchive();
        
        if($saved_zip_archive->open("{$this->getFullSavedBasePath()}.zip") === true) {
            $saved_zip_archive->extractTo($this->getFullTempBasePath());
            
            $saved_zip_archive->close();
        }
    }
    
    abstract public function getTempFile();

    public function copySavedToS3() {        
        $saved_file_path = "{$this->getSavedBasePath()}.zip";
        
        if($this->file_storage_engine->exists($saved_file_path)) {
            Storage::disk('s3')->put("{$saved_file_path}", $this->file_storage_engine->get($saved_file_path));
        }
    }
}
