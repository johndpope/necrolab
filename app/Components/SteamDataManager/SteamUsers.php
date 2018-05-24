<?php

namespace App\Components\SteamDataManager;

use DateTime;
use ZipArchive;
use stdClass;
use Illuminate\Support\Facades\Storage;
use App\Components\SteamDataManager\Core;

class SteamUsers
extends Core {  
    protected $temp_files = [];

    public function __construct(DateTime $date) {
        $this->file_extension = 'json';
        $this->base_path = 'steam_user_json';
    
        parent::__construct($date);
    }
    
    public function saveTempFile(string $file_name, string $contents) {
        $this->file_storage_engine->put("{$this->temp_base_path}/{$file_name}.{$this->file_extension}", $contents);
    }
    
    public function getTempFiles() {
        if(empty($this->temp_files)) {
            $all_temp_files = $this->file_storage_engine->allFiles($this->temp_base_path);
        
            if(!empty($all_temp_files)) {
                foreach($all_temp_files as $temp_file) {
                    $this->temp_files[] = [
                        'path' => $temp_file,
                        'full_path' => "{$this->storage_path}/{$temp_file}"
                    ];
                }
            }
        }

        return $this->temp_files;
    }
    
    public function getTempEntry() {
        $temp_files = $this->getTempFiles();
        
        if(!empty($temp_files)) {
            $local_storage = $this->file_storage_engine;
        
            foreach($temp_files as $temp_file) {
                $file_contents = $local_storage->get($temp_file['path']);
                
                yield json_decode($file_contents);
            }
        }
    }
}