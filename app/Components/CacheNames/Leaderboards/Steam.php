<?php

namespace App\Components\CacheNames\Leaderboards;

use DateTime;
use App\Components\CacheNames\Core;

class Steam
extends Core {
    const LEADERBOARDS = 'l';
    
    const RECORDS = 'r';
    
    const REPLAYS = 'sr';
    
    const SNAPSHOTS = 'ss';
    
    const DAILIES = 'd';
    
    const TYPES = 't';
    
    const GROUPED_IDS = 'gid';
    
    public static function getLeaderboards() {
        return static::LEADERBOARDS;
    }
    
    public static function getTypes() {
        return static::getLeaderboards() . ':' . static::TYPES;
    }
    
    public static function getRecords() {
        return static::getLeaderboards() . ':' . static::RECORDS;
    }
    
    public static function getRecordsIndex(array $index_segments) {                
        return parent::getIndex(static::getLeaderboards() . ':' . static::INDEX, $index_segments);
    }
    
    public static function getIds() {
        return static::getLeaderboards() . ':'  . static::IDS;
    }
    
    public static function getGroupedIds() {
        return static::getLeaderboards() . ':'  . static::GROUPED_IDS . ':' . static::IDS;
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
    
    public static function getSnapshots() {
        return static::getLeaderboards() . ':' . static::SNAPSHOTS;
    }
    
    public static function getAllSnapshots() {
        return static::getSnapshots();
    }
    
    public static function getSnapshotsIndex($leaderboard_id) {
        return parent::getIndex(static::getSnapshots() . ':' . static::INDEX, array(
            $leaderboard_id
        ));
    }
    
    public static function getReplays() {
        return static::REPLAYS;
    }
    
    public static function getAllReplays() {
        return static::getReplays() . ':' . static::RECORDS;
    }
    
    public static function getReplaysIndex(array $index_segments = array()) {
        return parent::getIndex(static::getReplays() . ':' . static::INDEX, $index_segments);
    }
}