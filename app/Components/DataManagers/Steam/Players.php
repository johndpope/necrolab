<?php

namespace App\Components\DataManagers\Steam;

use DateTime;
use Illuminate\Support\Facades\Storage;
use App\Components\DataManagers\Core;
use App\LeaderboardSources;

class Players
extends Core {  
    protected $date;

    protected $temp_files = [];

    public function __construct(DateTime $date) {
        $leaderboard_source = LeaderboardSources::where('name', 'steam')->firstOrFail();
        
        parent::__construct($leaderboard_source);
        
        $this->addBasePathSegment('players');
        
        $this->date = $date;
    }
    
    public function getSavedBasePath() {
        return parent::getSavedBasePath() . "/{$this->date->format('Y-m-d')}";
    }
    
    public function getTempBasePath() {
        return parent::getTempBasePath() . "/{$this->date->format('Y-m-d')}";
    }
    
    public function copySavedToS3() {}
    
    public function compressTempToSaved() {}
    
    public function getTempFile() {}
    
    public function saveTempFile(string $file_name, string $contents) {
        $this->file_storage_engine->put("{$this->getTempBasePath()}/{$file_name}.json", $contents);
    }
    
    protected function loadTempFiles() {
        $all_temp_files = $this->file_storage_engine->allFiles($this->getTempBasePath());
    
        if(!empty($all_temp_files)) {
            foreach($all_temp_files as $temp_file) {
                $this->temp_files[] = [
                    'path' => $temp_file,
                    'full_path' => "{$this->storage_path}/{$temp_file}"
                ];
            }
        }
    }
    
    public function getTempEntry() {
        $temp_files = $this->getTempFiles();
        
        if(!empty($temp_files)) {        
            foreach($temp_files as $temp_file) {
                $file_contents = $this->file_storage_engine->get($temp_file['path']);
                
                yield json_decode($file_contents);
            }
        }
    }
}
