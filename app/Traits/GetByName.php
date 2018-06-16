<?php

namespace App\Traits;

use App\Components\PostgresCursor;

trait GetByName {
    protected static $all_by_name = [];
    
    protected static function loadAllByName() {
        if(empty(static::$all_by_name)) {
            $all_records = static::all();
            
            if(!empty($all_records)) {
                foreach($all_records as $record) {
                    static::$all_by_name[$record->name] = $record;
                }
            }
        }
    }

    public static function getAllByName() {
        static::loadAllByName();
        
        return static::$all_by_name;
    }
    
    public static function getByName($name) {
        static::loadAllByName();
        
        $record = NULL;
        
        if(isset(static::$all_by_name[$name])) {
            $record = static::$all_by_name[$name];
        }
        
        return $record;
    }
    
    public static function getAllIdsByName() {
        $table_name = (new static())->getTable();
        $primary_key = (new static())->getKeyName();
    
        $query = static::select([
            'name',
            $primary_key
        ]);
        
        $cursor = new PostgresCursor(
            "{$table_name}_ids_by_name", 
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