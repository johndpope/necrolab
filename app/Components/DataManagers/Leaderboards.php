<?php

namespace App\Components\DataManagers;

use DateTime;
use ZipArchive;
use Illuminate\Support\Facades\Storage;
use App\LeaderboardSources;

abstract class Leaderboards
extends Core {
    protected $date;
    
    protected $file_extension = '';
    
    protected $temp_files = [];

    public function __construct(LeaderboardSources $leaderboard_source, string $file_extension, DateTime $date) {
        parent::__construct($leaderboard_source);
        
        $this->addBasePathSegment('leaderboards');
    
        $this->date = $date;
        
        $this->file_extension = $file_extension;
        
        $this->addBasePathSegment($this->file_extension);
    }
    
    public function getSavedBasePath() {
        return parent::getSavedBasePath() . "/{$this->date->format('Y-m-d')}";
    }
    
    public function getTempBasePath() {
        return parent::getTempBasePath() . "/{$this->date->format('Y-m-d')}";
    }
    
    public function getDate() {
        return $this->date;
    }
    
    abstract public function getTempLeaderboard();
    
    abstract public function getTempEntry(string $leaderboard_id);
}
