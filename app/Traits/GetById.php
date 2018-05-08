<?php

namespace App\Traits;

trait GetById {
    protected static $all_by_id = [];
    
    protected static function loadAllById() {
        if(empty(static::$all_by_id)) {
            $primary_key_name = (new static())->getKeyName();
        
            $all_records = static::all();
            
            if(!empty($all_records)) {
                foreach($all_records as $record) {
                    static::$all_by_id[$record->$primary_key_name] = $record;
                }
            }
        }
    }

    public static function getAllById() {
        static::loadAllById();
        
        return static::$all_by_id;
    }
    
    public static function getById($id) {
        static::loadAllById();
        
        $record = NULL;
        
        if(isset(static::$all_by_id[$id])) {
            $record = static::$all_by_id[$id];
        }
        
        return $record;
    }
}