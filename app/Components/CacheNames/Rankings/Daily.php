<?php

namespace App\Components\CacheNames\Rankings;

use App\Components\CacheNames\Core;
use App\Components\CacheNames\Players as PlayerCacheNames;
use App\Components\CacheNames\Prefix;

class Daily
extends Core {            
    const DAILY_RANKINGS = 'da';
    
    const TOTAL_POINTS = 'tp';
    
    public static function getBase(Prefix $prefix) {
        return static::DAILY_RANKINGS . ":" . (string)$prefix;
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
}
