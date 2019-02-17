<?php

namespace App\Traits;

use PDO;
use App\Components\RecordQueue;
use App\Components\InsertQueue;
use App\LeaderboardSources;

trait HasTempTable {    
    protected static $temp_table_name = [];
    
    public static function loadTempTableName(LeaderboardSources $leaderboard_source): void {
        if(!isset(static::$temp_table_name[$leaderboard_source->name])) {
            $instance = new static();
            
            $instance->setSchema($leaderboard_source->name);
        
            static::$temp_table_name[$leaderboard_source->name] = str_replace('.', '_', $instance->getTable()) . '_temp';
        }
    }
    
    public static function getTempTableName(LeaderboardSources $leaderboard_source): string {
        static::loadTempTableName($leaderboard_source);
        
        return static::$temp_table_name[$leaderboard_source->name];
    }
    
    public static function getTempInsertQueueBindFlags(): array {
        return [];
    }
    
    public static function getTempInsertQueue(LeaderboardSources $leaderboard_source, int $commit_count): RecordQueue {        
        $record_queue = new RecordQueue($commit_count);
        
        $insert_queue = new InsertQueue(static::getTempTableName($leaderboard_source));
        
        $insert_queue->setParameterBindings(static::getTempInsertQueueBindFlags());
        
        $insert_queue->addToRecordQueue($record_queue);
    
        return $record_queue;
    }
    
    abstract public static function createTemporaryTable(LeaderboardSources $leaderboard_source): void;
    
    abstract public static function saveNewTemp(LeaderboardSources $leaderboard_source): void;
    
    abstract public static function updateFromTemp(LeaderboardSources $leaderboard_source): void;
}
