<?php

namespace App\Traits;

use DateTime;
use Illuminate\Support\Facades\Cache;

trait StoredInCache {
    protected static $cached_records = [];
    
    protected static function getStoredInCacheQuery() {
        return static::orderBy('id', 'asc');
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
    
    protected static function clearCache() {
        $opcache = Cache::store('opcache');
    
        $cache_key = (new static())->getTable();
        
        $opcache->forget($cache_key);
    }
    
    public static function refreshCache() {
        static::clearCache();
        
        static::loadAllFromCache();
    }
    
    public static function all($columns = []) {
        static::loadAllFromCache();
        
        return static::$cached_records;
    }
}
