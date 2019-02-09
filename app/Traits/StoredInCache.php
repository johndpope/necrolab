<?php

namespace App\Traits;

use DateTime;
use ElcoBvg\Opcache\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

trait StoredInCache {
    protected static $cached_records = [];
    
    protected static function getStoredInCacheQuery(): Builder {
        return static::orderBy('id', 'asc');
    }
    
    protected static function getCacheTtl(): DateTime {
        return new DateTime('+1 year');
    }
    
    protected static function processDataBeforeCache(Collection $records): void {}

    protected static function loadAllFromCache() {
        if(empty(static::$cached_records)) {
            $opcache = Cache::store('opcache');
            
            $cache_key = (new static())->getTable();
            
            static::$cached_records = $opcache->remember($cache_key, static::getCacheTtl(), function() {
                $records = static::getStoredInCacheQuery()->get();
                
                static::processDataBeforeCache($records);
                
                return $records;
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
