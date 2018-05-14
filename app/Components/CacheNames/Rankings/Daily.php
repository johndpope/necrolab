<?php

namespace App\Components\CacheNames\Rankings;

use App\Components\CacheNames\Core;

class Daily
extends Core {            
    const DAILY_RANKINGS = 'da';
    
    const DAY_TYPE = 'd';
    
    const TOTAL_POINTS = 'tp';
    
    const NUMBER_OF_DAYS = 'nd';
    
    public static function getModes($release_id) {
        return static::DAILY_RANKINGS . ":{$release_id}:" . static::MODES;
    }
    
    public static function getBase($release_id, $mode_id) {
        return static::DAILY_RANKINGS . ":{$release_id}:{$mode_id}";
    }
    
    public static function getModeNumberOfDays($release_id, $mode_id) {
        return static::getBase($release_id, $mode_id) . ':' . static::NUMBER_OF_DAYS;
    }
    
    public static function getRankings($release_id, $mode_id, $number_of_days) {
        return static::getBase($release_id, $mode_id) . ":{$number_of_days}";
    }

    public static function getEntries($release_id, $mode_id, $number_of_days) {
        return static::getRankings($release_id, $mode_id, $number_of_days) . ':' . static::ENTRIES;
    }
    
    public static function getEntry($release_id, $mode_id, $number_of_days, $steamid) {    
        return static::getEntries($release_id, $mode_id, $number_of_days) . ":{$steamid}";
    }
    
    public static function getTotalPoints($release_id, $mode_id, $number_of_days) {    
        return static::getRankings($release_id, $mode_id, $number_of_days) . ':' . static::TOTAL_POINTS;
    }
    
    public static function getEntriesIndex($release_id, $mode_id, $number_of_days, array $index_segments = array()) {
        return parent::getIndex(static::getEntries($release_id, $mode_id, $number_of_days) . ':' . static::INDEX, $index_segments);
    }
}