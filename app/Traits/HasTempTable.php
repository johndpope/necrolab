<?php

namespace App\Traits;

use App\Components\RecordQueue;
use App\Components\InsertQueue;

trait HasTempTable {    
    protected static $temp_table_name;
    
    public static function loadTempTableName() {
        if(!isset(static::$temp_table_name)) {        
            static::$temp_table_name = (new static())->getTable() . '_temp';
        }
    }
    
    public static function getTempTableName() {
        static::loadTempTableName();
        
        return static::$temp_table_name;
    }
    
    public static function getTempInsertQueue(int $commit_count) {        
        $record_queue = new RecordQueue($commit_count);
        
        $insert_queue = new InsertQueue(static::getTempTableName());
        
        $insert_queue->addToRecordQueue($record_queue);
    
        return $record_queue;
    }
}