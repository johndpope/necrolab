<?php

namespace App\Components\CacheNames;

class Core {    
    protected const ENTRIES = 'e';
    
    protected const INDEX = 'idx';
    
    protected const IDS = 'ids';
    
    protected const NO_ID = '0';
    
    protected const SCORE = 'sc';
    
    protected const SPEED = 'sp';
    
    protected const DEATHLESS = 'de';
    
    protected const DAILY = 'da';
    
    protected const MODES = 'mo';
    
    protected const CHARACTER = 'ch';
    
    public static function getIndex($base_name, array $index_segments) {
        $index_name = implode(':', $index_segments);
        
        $cache_name = $base_name;
        
        if(!empty($index_segments)) {
            $cache_name .= ":{$index_name}";
        }
    
        return $cache_name;
    }
    
    public static function getCharacters() {
        return static::CHARACTER;
    }
}