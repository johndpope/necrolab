<?php

namespace App\Components\SteamLeaderboardDataManager;

use DateTime;
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
    
    public static function runClientDownloader(string $executable_path, string $appid, string $username, string $password, string $names_path) {        
        $save_path = dirname($names_path);
        
        exec("cd {$save_path} && /usr/bin/mono {$executable_path} {$username} {$password} {$appid} {$names_path}");
        
        unset($password);
    }
}