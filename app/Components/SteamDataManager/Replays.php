<?php

namespace App\Components\SteamDataManager;

use DateTime;
use ZipArchive;
use stdClass;
use Illuminate\Support\Facades\Storage;
use App\Components\SteamDataManager\Core;

class Replays
extends Core {  
    protected $temp_files = [];
    
    protected $invalid_files = [];
    
    protected $invalid_base_path;

    public function __construct() {
        $this->file_extension = '';
        $this->base_path = 'steam_replays';
    
        parent::__construct(new DateTime());
        
        $this->saved_base_path = "{$this->base_path}";
        
        $this->temp_base_path = "{$this->base_path}/temp";
        
        $this->invalid_base_path = "{$this->base_path}/invalid";
        
        $this->s3_base_path = "{$this->base_path}/s3_queue";
    }
    
    public function saveTempFile(int $steam_user_pb_id, string $ugcid, string $contents) {
        $this->file_storage_engine->put("{$this->temp_base_path}/{$steam_user_pb_id}_{$ugcid}", $contents);
    }
    
    public function saveInvalidFile(int $steam_user_pb_id) {
        $this->file_storage_engine->put("{$this->invalid_base_path}/{$steam_user_pb_id}", '');
    }
    
    public function getTempFiles() {
        if(empty($this->temp_files)) {
            $all_temp_files = $this->file_storage_engine->allFiles($this->temp_base_path);
        
            if(!empty($all_temp_files)) {
                foreach($all_temp_files as $temp_file) {
                    $temp_file_entry = new stdClass();
                    
                    $temp_file_entry->path = $temp_file;
                    $temp_file_entry->full_path = "{$this->storage_path}/{$temp_file_entry->path}";
                    
                    $file_name = basename($temp_file_entry->path, '.replay');
                
                    $file_name_split = explode('_', $file_name);
                    
                    $temp_file_entry->steam_user_pb_id = (int)$file_name_split[0];
                    $temp_file_entry->ugcid = $file_name_split[1];
                    $temp_file_entry->contents = $this->file_storage_engine->get($temp_file_entry->path);
                
                    $this->temp_files[] = $temp_file_entry;
                }
            }
        }

        return $this->temp_files;
    }
    
    public function getTempFile() {
        $temp_files = $this->getTempFiles();
        
        if(!empty($temp_files)) {        
            foreach($temp_files as $temp_file) { 
                yield $temp_file;
            }
        }
    }
    
    public function getInvalidFiles() {
        if(empty($this->invalid_files)) {
            $all_invalid_files = $this->file_storage_engine->allFiles($this->invalid_base_path);
        
            if(!empty($all_invalid_files)) {
                foreach($all_invalid_files as $invalid_file) {
                    $invalid_file_entry = new stdClass();
                    
                    $invalid_file_entry->path = $invalid_file;
                    $invalid_file_entry->full_path = "{$this->storage_path}/{$invalid_file_entry->path}";
                    $invalid_file_entry->steam_user_pb_id = basename($invalid_file_entry->full_path, '.replay');
                
                    $this->invalid_files[] = $invalid_file_entry;
                }
            }
        }

        return $this->invalid_files;
    }
    
    public function getInvalidFile() {
        $invalid_files = $this->getInvalidFiles();

        if(!empty($invalid_files)) {        
            foreach($invalid_files as $invalid_file) {
                yield $invalid_file;
            }
        }
    }
    
    public function compressTempFileToSaved(object $temp_file) {
        $saved_zip_archive = new ZipArchive();

        $saved_zip_archive->open("{$this->getFullSavedBasePath()}/{$temp_file->ugcid}.zip", ZipArchive::CREATE | ZipArchive::OVERWRITE);
        
        $relative_path = $temp_file->ugcid;
        
        $saved_zip_archive->addFile($temp_file->full_path, $relative_path);
        
        $saved_zip_archive->close();            
    }
    
    public function deleteTempFile(object $temp_file) {
        $this->file_storage_engine->delete($temp_file->path);
    }
    
    public function deleteInvalidFile(object $invalid_file) {
        $this->file_storage_engine->delete($invalid_file->path);
    }
    
    public function copySavedFileToS3($ugcid) {        
        $saved_file_path = "{$this->saved_base_path}/{$ugcid}.zip";
        
        if($this->file_storage_engine->exists($saved_file_path)) {
            Storage::disk('s3')->put("replays/{$ugcid}.zip", $this->file_storage_engine->get($saved_file_path));
        }
    }
}