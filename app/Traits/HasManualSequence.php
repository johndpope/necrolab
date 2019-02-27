<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use App\LeaderboardSources;

trait HasManualSequence {    
    protected static $sequence_name = [];
    
    public static function getNewRecordId(LeaderboardSources $leaderboard_source) {
        if(!isset(static::$sequence_name[$leaderboard_source->name])) {
            $instance = new static();
            
            $instance->setSchema($leaderboard_source->name);
        
            static::$sequence_name[$leaderboard_source->name] = "{$instance->getTable()}_seq";
        }
    
        $new_record_id = DB::selectOne("
            SELECT nextval('" . static::$sequence_name[$leaderboard_source->name] . "'::regclass) AS id
        ");
        
        return $new_record_id->id;
    }
}
