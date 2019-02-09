<?php

namespace App\Components\CacheNames\Rankings;

use App\Components\CacheNames\Core;
use App\Components\CacheNames\Players as PlayerCacheNames;
use App\Components\CacheNames\Prefix;

class Power
extends Core {            
    protected const POWER_RANKING = 'pr';
    
    protected const CATEGORY = 'ca';
    
    protected const TOTAL_POINTS = 'tp';
    
    public static function getBase(Prefix $prefix) {
        return static::POWER_RANKING . ':' . (string)$prefix;
    }
    
    public static function getPlayer($player_id, Prefix $prefix) {
        return PlayerCacheNames::getPlayer($player_id) . ':' . static::getBase($prefix);
    }
    
    public static function getEntries(Prefix $prefix) {
        return static::getBase($prefix) . ':' . static::ENTRIES;
    }
    
    public static function getEntry(Prefix $prefix, $player_id) {
        return static::getEntries($prefix) . ":{$player_id}";
    }
    
    public static function getTotalPoints(Prefix $prefix) {
        return static::getBase($prefix) . ':' . static::TOTAL_POINTS;
    }
    
    public static function getCategory(Prefix $prefix, $leaderboard_type_id) {
        return static::getBase($prefix) . ':' . static::CATEGORY . ":{$leaderboard_type_id}";
    }
    
    public static function getPlayerCategory(Prefix $prefix, $player_id, $leaderboard_type_id) {
        return PlayerCacheNames::getPlayer($player_id) . ':' . static::getCategory($prefix, $leaderboard_type_id);
    }
    
    public static function getCategoryPoints(Prefix $prefix, $leaderboard_type_id) {
        return static::getCategory($prefix, $leaderboard_type_id) . ':' . static::TOTAL_POINTS;
    }
    
    public static function getCharacter(Prefix $prefix, $character_name) {
        return static::getBase($prefix) . ':' . static::CHARACTER . ":{$character_name}";
    }
    
    public static function getPlayerCharacter(Prefix $prefix, $player_id, $character_name) {
        return PlayerCacheNames::getPlayer($player_id) . ':' . static::getCharacter($prefix, $character_name);
    }
    
    public static function getCharacterPoints(Prefix $prefix, $character_name) {
        return static::getCharacter($prefix, $character_name) . ':' . static::TOTAL_POINTS;
    }
}
