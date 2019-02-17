<?php

namespace App\Traits;

use Exception;
use Illuminate\Database\Eloquent\Model;
use App\LeaderboardSources;

trait HasDefaultRecord {    
    protected static $default_record;
    
    protected static function loadDefaultRecord(): void {
        if(!isset(static::$default_record)) {
            $default_record = static::where('is_default', 1)
                ->first();
            
            if(empty($default_record)) {
                throw new Exception("A default record does not exist for this table.");
            }
        
            static::$default_record = $default_record;
        }
    }
    
    public static function getDefaultRecord(): Model {
        static::loadDefaultRecord();
        
        return static::$default_record;
    }
}
