<?php

namespace App\Components\CacheNames;

use App\Components\CacheNames\Core;
use App\Components\CacheNames\Prefix;

class Leaderboards
extends Core {
    const LEADERBOARDS = 'l';
    
    const RECORDS = 'r';
    
    const NON_DAILY = 'nd';
    
    public static function getLeaderboards() {
        return static::LEADERBOARDS . ':' . static::NON_DAILY;
    }
    
    public static function getRecords() {
        return static::getLeaderboards() . ':' . static::RECORDS;
    }
    
    public static function getEntries($leaderboard_id) {
        return static::getLeaderboards() . ":{$leaderboard_id}:" . static::ENTRIES;
    }
    
    public static function getIndex($leaderboard_id, array $index_segments) {                
        return parent::getIndex(static::getEntries($leaderboard_id) . ':' . static::INDEX, $index_segments);
    }    
    
    public static function getDailyLeaderboards(Prefix $prefix) {
        return static::LEADERBOARDS . ':' . static::DAILY . ':' . (string)$prefix;
    }
    
    public static function getDailyEntries(Prefix $prefix) {
        return static::getDailyLeaderboards($prefix) . ':' . static::ENTRIES;
    }
}
