<?php

namespace App\Components\CacheNames\Rankings;

use App\Components\CacheNames\Core;
use App\Components\CacheNames\Players as PlayerCacheNames;

class Power
extends Core {            
    protected const POWER_RANKING = 'pr';
    
    protected const TOTAL_POINTS = 'tp';
    
    public static function getModes($leaderboard_source_id, $release_id, $seeded) {
        return static::POWER_RANKING . ":{$leaderboard_source_id}:{$release_id}:{$seeded}:" . static::MODES;
    }
    
    public static function getBase($leaderboard_source_id, $release_id, $mode_id, $seeded) {
        return static::POWER_RANKING . ":{$leaderboard_source_id}:{$release_id}:{$mode_id}:{$seeded}";
    }
    
    public static function getPlayer($player_id, $leaderboard_source_id, $release_id, $mode_id, $seeded) {
        return PlayerCacheNames::getPlayer($player_id) . ':' . static::getBase($leaderboard_source_id, $release_id, $mode_id, $seeded);
    }
    
    public static function getEntries($leaderboard_source_id, $release_id, $mode_id, $seeded) {
        return static::getBase($leaderboard_source_id, $release_id, $mode_id, $seeded) . ':' . static::ENTRIES;
    }
    
    public static function getEntry($leaderboard_source_id, $release_id, $mode_id, $seeded, $steam_user_id) {
        return static::getEntries($leaderboard_source_id, $release_id, $mode_id, $seeded) . ":{$steam_user_id}";
    }
    
    public static function getTotalPoints($leaderboard_source_id, $release_id, $mode_id, $seeded) {
        return static::getBase($leaderboard_source_id, $release_id, $mode_id, $seeded) . ':' . static::TOTAL_POINTS;
    }
    
    public static function getCategory($leaderboard_source_id, $leaderboard_type_id, $release_id, $mode_id, $seeded) {
        return static::getBase($leaderboard_source_id, $release_id, $mode_id, $seeded) . ":{$leaderboard_type_id}";
    }
    
    public static function getPlayerCategory($player_id, $leaderboard_source_id, $leaderboard_type_id, $release_id, $mode_id, $seeded) {
        return PlayerCacheNames::getPlayer($player_id) . ':' . static::getCategory($leaderboard_source_id, $leaderboard_type_id, $release_id, $mode_id, $seeded);
    }
    
    public static function getCategoryPoints($leaderboard_source_id, $release_id, $mode_id, $seeded) {
        return static::getCategory($leaderboard_source_id, $leaderboard_type_id, $release_id, $mode_id, $seeded) . ':' . static::TOTAL_POINTS;
    }
    
    public static function getCharacter($leaderboard_source_id, $release_id, $mode_id, $seeded, $character_name) {
        return static::getBase($leaderboard_source_id, $release_id, $mode_id, $seeded) . ':' . static::CHARACTER . ":{$character_name}";
    }
    
    public static function getPlayerCharacter($player_id, $leaderboard_source_id, $release_id, $mode_id, $seeded, $character_name) {
        return PlayerCacheNames::getPlayer($player_id) . ':' . static::getCharacter($leaderboard_source_id, $release_id, $mode_id, $seeded, $character_name);
    }
    
    public static function getCharacterPoints($leaderboard_source_id, $release_id, $mode_id, $seeded, $character_name) {
        return static::getCharacter($leaderboard_source_id, $release_id, $mode_id, $seeded, $character_name) . ':' . static::TOTAL_POINTS;
    }
    
    public static function getIndex($base_index_name, array $index_segments) {
        return parent::getIndex("{$base_index_name}:" . static::INDEX, $index_segments);
    }
}
