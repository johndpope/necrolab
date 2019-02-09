<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use App\Components\PostgresCursor;
use App\LeaderboardSources;

trait SchemaGetByName {
    protected static $all_by_name = [];
    
    protected static function loadAllByName(LeaderboardSources $leaderboard_source): void {    
        if(empty(static::$all_by_name[$leaderboard_source->name])) {
            static::$all_by_name[$leaderboard_source->name] = [];
        
            $table_name = "{$leaderboard_source->name}." . (new static())->getTable();
        
            $all_records = DB::table($table_name)->get();
            
            if(!empty($all_records)) {
                foreach($all_records as $record) {
                    static::$all_by_name[$leaderboard_source->name][$record->name] = $record;
                }
            }
        }
    }

    public static function getAllByName(LeaderboardSources $leaderboard_source): array {
        static::loadAllByName($leaderboard_source);
        
        return static::$all_by_name[$leaderboard_source->name];
    }
    
    public static function getByName(LeaderboardSources $leaderboard_source, string $name): ?\Illuminate\Database\Eloquent\Model {
        static::loadAllByName($leaderboard_source);
        
        $record = NULL;
        
        if(isset(static::$all_by_name[$leaderboard_source->name][$name])) {
            $record = static::$all_by_name[$leaderboard_source->name][$name];
        }
        
        return $record;
    }
    
    public static function getAllIdsByName(LeaderboardSources $leaderboard_source): array {
        $table_name = "{$leaderboard_source->name}." . (new static())->getTable();
        $primary_key = (new static())->getKeyName();
    
        $query = DB::table($table_name)->select([
            'name',
            $primary_key
        ]);
        
        $cursor_name_prefix = str_replace('.', '_', $table_name);
        
        $cursor = new PostgresCursor(
            "{$cursor_name_prefix}_ids_by_name", 
            $query,
            1000
        );
        
        $ids_by_name = [];
        
        foreach($cursor->getRecord() as $record) {
            $ids_by_name[$record->name] = $record->$primary_key;
        }
        
        return $ids_by_name;
    }
}
