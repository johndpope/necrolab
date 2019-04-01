<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use App\LeaderboardSources;

trait HasManualSequence {    
    protected static $sequence_name = [];
    
    protected static function loadSequenceName(LeaderboardSources $leaderboard_source): void {
        if(!isset(static::$sequence_name[$leaderboard_source->name])) {
            $instance = new static();
            
            $instance->setSchema($leaderboard_source->name);
        
            static::$sequence_name[$leaderboard_source->name] = "{$instance->getTable()}_seq";
        }
    }
    
    public static function getSequenceName(LeaderboardSources $leaderboard_source): string {
        static::loadSequenceName($leaderboard_source);
        
        return static::$sequence_name[$leaderboard_source->name];
    }
    
    public static function getNewRecordId(LeaderboardSources $leaderboard_source): int {
        $sequence_name = static::getSequenceName($leaderboard_source);
    
        $new_record_id = DB::selectOne("
            SELECT nextval('{$sequence_name}'::regclass) AS id
        ");
        
        return $new_record_id->id;
    }
    
    public static function syncManualSequence(LeaderboardSources $leaderboard_source): void {        
        $sequence_name = static::getSequenceName($leaderboard_source);
        
        $table_name = str_replace('_seq', '', $sequence_name);

        DB::statement("
            SELECT setval('{$sequence_name}'::regclass, COALESCE((
                SELECT MAX(id) + 1
                FROM {$table_name}
            ), 1))
        ");
    }
}
