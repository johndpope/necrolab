<?php

namespace App\Components\CacheNames\Rankings;

use App\Components\CacheNames\Core;
use App\Components\CacheNames\Players as PlayerCacheNames;

class Daily
extends Core {            
    const DAILY_RANKINGS = 'da';
    
    const DAY_TYPE = 'd';
    
    const TOTAL_POINTS = 'tp';
    
    const NUMBER_OF_DAYS = 'nd';
    
    public static function getModes($leaderboard_source_id, $release_id) {
        return static::DAILY_RANKINGS . ":{$leaderboard_source_id}:{$release_id}:" . static::MODES;
    }
    
    public static function getBase($leaderboard_source_id, $release_id, $mode_id) {
        return static::DAILY_RANKINGS . ":{$leaderboard_source_id}:{$release_id}:{$mode_id}";
    }
    
    public static function getModeNumberOfDays($leaderboard_source_id, $release_id, $mode_id) {
        return static::getBase($leaderboard_source_id, $release_id, $mode_id) . ':' . static::NUMBER_OF_DAYS;
    }
    
    public static function getRankings($leaderboard_source_id, $release_id, $mode_id, $number_of_days) {
        return static::getBase($leaderboard_source_id, $release_id, $mode_id) . ":{$number_of_days}";
    }
    
    public static function getPlayerRankings($player_id, $leaderboard_source_id, $release_id, $mode_id, $number_of_days) {
        return PlayerCacheNames::getPlayer($player_id) . ':' . static::getRankings($leaderboard_source_id, $release_id, $mode_id, $number_of_days);
    }

    public static function getEntries($leaderboard_source_id, $release_id, $mode_id, $number_of_days) {
        return static::getRankings($leaderboard_source_id, $release_id, $mode_id, $number_of_days) . ':' . static::ENTRIES;
    }
    
    public static function getEntry($leaderboard_source_id, $release_id, $mode_id, $number_of_days, $steamid) {    
        return static::getEntries($leaderboard_source_id, $release_id, $mode_id, $number_of_days) . ":{$steamid}";
    }
    
    public static function getTotalPoints($leaderboard_source_id, $release_id, $mode_id, $number_of_days) {    
        return static::getRankings($leaderboard_source_id, $release_id, $mode_id, $number_of_days) . ':' . static::TOTAL_POINTS;
    }
    
    public static function getEntriesIndex($leaderboard_source_id, $release_id, $mode_id, $number_of_days, array $index_segments = array()) {
        return parent::getIndex(static::getEntries($leaderboard_source_id, $release_id, $mode_id, $number_of_days) . ':' . static::INDEX, $index_segments);
    }
}
