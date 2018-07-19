<?php

namespace App\Traits;

use DateTime;
use Illuminate\Support\Facades\Cache;

trait StoredInCache {
    protected static $cached_records = [];
    
    protected static function getStoredInCacheQuery() {
        return static::where('1', 1);
    }

    protected static function loadAllFromCache() {
        if(empty(static::$cached_records)) {
            $opcache = Cache::store('opcache');
            
            $cache_key = (new static())->getTable();
            
            static::$cached_records = $opcache->remember($cache_key, new DateTime('+1 year'), function() {
                return static::getStoredInCacheQuery()->get();
            });
        }
    }
    
    public static function all($columns = []) {
        static::loadAllFromCache();
        
        return static::$cached_records;
    }
}