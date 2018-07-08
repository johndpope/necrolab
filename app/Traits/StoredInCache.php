<?php

namespace App\Traits;

use DateTime;
use Illuminate\Support\Facades\Cache;

trait StoredInCache {
    public static function getCacheQuery() {
        return NULL;
    }

    public static function getAllFromCache() {        
        $opcache = Cache::store('opcache');
        
        $cache_key = (new static())->getTable();
        
        return $opcache->remember($cache_key, new DateTime('+1 year'), function() {
            return static::getCacheQuery()->get();
        });
    }
}