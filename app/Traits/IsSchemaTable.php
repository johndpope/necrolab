<?php

namespace App\Traits;

use App\LeaderboardSources;

trait IsSchemaTable {    
    protected static $schema_table_names = [];
    
    public static function loadSchemaTableName(LeaderboardSources $leaderboard_source): void {
        if(!isset(static::$schema_table_names[$leaderboard_source->name])) {        
            static::$schema_table_names[$leaderboard_source->name] = "{$leaderboard_source->name}." . (new static())->getTable();
        }
    }
    
    public static function getSchemaTableName(LeaderboardSources $leaderboard_source): string {
        static::loadSchemaTableName($leaderboard_source);
        
        return static::$schema_table_names[$leaderboard_source->name];
    }
}
