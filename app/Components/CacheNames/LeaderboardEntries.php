<?php

namespace App\Components\CacheNames\Leaderboards;

use DateTime;
use App\Components\CacheNames\Core;

class Steam
extends Core {
    const LEADERBOARDS = 'l';
    
    const DAILIES = 'd';
    
    const TYPES = 't';
    
    public static function getLeaderboards() {
        return static::LEADERBOARDS;
    }
    
    public static function getEntries($leaderboard_id) {
        return static::getLeaderboards() . ':' . static::ENTRIES . ":{$leaderboard_id}";
    }
    
    public static function getIndex($leaderboard_id, array $index_segments) {                
        return parent::getIndex(static::getEntries($leaderboard_id) . ':' . static::INDEX, $index_segments);
    }    
    
    public static function getDailyLeaderboards() {
        return static::LEADERBOARDS . ':' . static::DAILIES;
    }
    
    public static function getDailyEntries() {
        return static::getDailyLeaderboards() . ':' . static::ENTRIES;
    }
    
    public static function getDailyIndex(DateTime $date, array $index_segments) {                
        return parent::getIndex(static::getDailyEntries($date) . ":{$date->format('Y-m-d')}:" . static::INDEX, $index_segments);
    }    
}
