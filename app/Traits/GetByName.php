<?php

namespace App\Traits;

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
}