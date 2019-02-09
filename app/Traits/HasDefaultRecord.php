<?php

namespace App\Traits;

use Exception;
use Illuminate\Database\Eloquent\Model;
use App\LeaderboardSources;

trait HasDefaultRecord {    
    protected static $default_records = [];
    
    protected static function loadDefaultRecord(LeaderboardSources $leaderboard_source): void {
        if(!isset(static::$default_records[$leaderboard_source->name])) {
            $default_record = static::where('is_default', 1)
                ->first();
            
            if(empty($default_record)) {
                throw new Exception("A default record exists for this table.");
            }
        
            static::$default_records[$leaderboard_source->name] = $default_record;
        }
    }
    
    public static function getDefaultRecord(LeaderboardSources $leaderboard_source): Model {
        static::loadDefaultRecord($leaderboard_source);
        
        return static::$default_records[$leaderboard_source->name];
    }
}
