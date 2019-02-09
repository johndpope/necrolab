<?php

namespace App\Components\DataManagers;

use Exception;
use ZipArchive;
use stdClass;
use Traversable;
use Illuminate\Support\Facades\Storage;
use App\LeaderboardSources;

class Replays
extends Core {
    protected $temp_files = [];
    
    protected $invalid_files = [];

    public function __construct(LeaderboardSources $leaderboard_source) {
        parent::__construct($leaderboard_source);
        
        $this->addBasePathSegment('replays');
    }
    
    public function getInvalidBasePath(): string {
        return parent::getSavedBasePath() . "/invalid";
    }
    
    public function saveTempFile(int $player_pb_id, string $external_id, string $contents): void {
        $this->file_storage_engine->put("{$this->getTempBasePath()}/{$player_pb_id}_{$external_id}", $contents);
    }
    
    public function saveInvalidFile(int $player_pb_id): void {
        $this->file_storage_engine->put("{$this->getInvalidBasePath()}/{$player_pb_id}", '');
    }
    
    public function getTempFile(): Traversable {
        $temp_files = $this->getTempFiles();
        
        if(!empty($temp_files)) {        
            foreach($temp_files as $temp_file) { 
                yield $temp_file;
            }
        }
    }
    
    protected function loadTempFiles(): void {
        if(empty($this->temp_files)) {
            $all_temp_files = $this->file_storage_engine->allFiles($this->getTempBasePath());
        
            if(!empty($all_temp_files)) {
                foreach($all_temp_files as $temp_file) {
                    $temp_file_entry = new stdClass();
                    
                    $temp_file_entry->path = $temp_file;
                    $temp_file_entry->full_path = "{$this->storage_path}/{$temp_file_entry->path}";
                    
                    $file_name = basename($temp_file_entry->path, '.replay');
                
                    $file_name_split = explode('_', $file_name);
                    
                    $temp_file_entry->player_pb_id = (int)$file_name_split[0];
                    $temp_file_entry->external_id = $file_name_split[1];
                    $temp_file_entry->contents = $this->file_storage_engine->get($temp_file_entry->path);
                
                    $this->temp_files[] = $temp_file_entry;
                }
            }
        }
    }
    
    public function getInvalidFiles(): array {
        if(empty($this->invalid_files)) {
            $all_invalid_files = $this->file_storage_engine->allFiles($this->getInvalidBasePath());
        
            if(!empty($all_invalid_files)) {
                foreach($all_invalid_files as $invalid_file) {
                    $invalid_file_entry = new stdClass();
                    
                    $invalid_file_entry->path = $invalid_file;
                    $invalid_file_entry->full_path = "{$this->storage_path}/{$invalid_file_entry->path}";
                    $invalid_file_entry->player_pb_id = basename($invalid_file_entry->full_path, '.replay');
                
                    $this->invalid_files[] = $invalid_file_entry;
                }
            }
        }

        return $this->invalid_files;
    }
    
    public function getInvalidFile(): Traversable {
        $invalid_files = $this->getInvalidFiles();

        if(!empty($invalid_files)) {        
            foreach($invalid_files as $invalid_file) {
                yield $invalid_file;
            }
        }
    }
    
    public function deleteTempFile(object $temp_file): void {
        $this->file_storage_engine->delete($temp_file->path);
    }
    
    public function deleteInvalidFile(object $invalid_file): void {
        $this->file_storage_engine->delete($invalid_file->path);
    }
    
    public function compressTempFileToSaved(object $temp_file): void {
        $saved_zip_archive = new ZipArchive();

        $saved_zip_archive->open("{$this->getFullSavedBasePath()}/{$temp_file->external_id}.zip", ZipArchive::CREATE | ZipArchive::OVERWRITE);
        
        $relative_path = $temp_file->external_id;
        
        $saved_zip_archive->addFile($temp_file->full_path, $relative_path);
        
        $saved_zip_archive->close();            
    }
    
    public function copySavedFileToS3(string $external_id): void {
        $saved_file_path = "{$this->getSavedBasePath()}/{$external_id}.zip";
        
        if($this->file_storage_engine->exists($saved_file_path)) {
            Storage::disk('s3')->put($saved_file_path, $this->file_storage_engine->get($saved_file_path));
        }
        else {
            throw new Exception("File '{$saved_file_path}' does not exist and cannot be uploaded to S3.");
        }
    }
    
    public function compressTempToSaved(): void {}
}
