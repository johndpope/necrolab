<?php

namespace App\Components\CacheNames\Rankings;

use App\Components\CacheNames\Core;
use App\Components\CacheNames\Players as PlayerCacheNames;

class Power
extends Core {            
    protected const POWER_RANKING = 'pr';
    
    protected const TOTAL_POINTS = 'tp';
    
    public static function getModes($release_id, $seeded) {
        return static::POWER_RANKING . ":{$release_id}:{$seeded}:" . static::MODES;
    }
    
    public static function getBase($release_id, $mode_id, $seeded) {
        return static::POWER_RANKING . ":{$release_id}:{$mode_id}:{$seeded}";
    }
    
    public static function getPlayer($player_id, $release_id, $mode_id, $seeded) {
        return PlayerCacheNames::getPlayer($player_id) . ':' . static::getBase($release_id, $mode_id, $seeded);
    }
    
    public static function getEntries($release_id, $mode_id, $seeded) {
        return static::getBase($release_id, $mode_id, $seeded) . ':' . static::ENTRIES;
    }
    
    public static function getEntry($release_id, $mode_id, $seeded, $steam_user_id) {
        return static::getEntries($release_id, $mode_id, $seeded) . ":{$steam_user_id}";
    }
    
    public static function getTotalPoints($release_id, $mode_id, $seeded) {
        return static::getBase($release_id, $mode_id, $seeded) . ':' . static::TOTAL_POINTS;
    }
    
    public static function getCategory($leaderboard_type_id, $release_id, $mode_id, $seeded) {
        return static::getBase($release_id, $mode_id, $seeded) . ":{$leaderboard_type_id}";
    }
    
    public static function getPlayerCategory($player_id, $leaderboard_type_id, $release_id, $mode_id, $seeded) {
        return PlayerCacheNames::getPlayer($player_id) . ':' . static::getCategory($release_id, $mode_id, $seeded);
    }
    
    public static function getCategoryPoints($release_id, $mode_id, $seeded) {
        return static::getCategory($leaderboard_type_id, $release_id, $mode_id, $seeded) . ':' . static::TOTAL_POINTS;
    }
    
    public static function getCharacter($release_id, $mode_id, $seeded, $character_name) {
        return static::getBase($release_id, $mode_id, $seeded) . ':' . static::CHARACTER . ":{$character_name}";
    }
    
    public static function getPlayerCharacter($player_id, $release_id, $mode_id, $seeded, $character_name) {
        return PlayerCacheNames::getPlayer($player_id) . ':' . static::getCharacter($release_id, $mode_id, $seeded, $character_name);
    }
    
    public static function getCharacterPoints($release_id, $mode_id, $seeded, $character_name) {
        return static::getCharacter($release_id, $mode_id, $seeded, $character_name) . ':' . static::TOTAL_POINTS;
    }
    
    public static function getIndex($base_index_name, array $index_segments) {
        return parent::getIndex("{$base_index_name}:" . static::INDEX, $index_segments);
    }
}
