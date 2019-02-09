<?php

namespace App\Traits;

use Exception;
use Illuminate\Database\Eloquent\Model;

trait CanBeGrouped {
    protected static $all_by_grouped = [];
    
    protected static function getGroupFields() {
        return [];
    }
    
    protected static function loadAllByGrouped() {
        if(empty(static::$all_by_grouped)) {        
            $all_records = static::all();
            
            if(!empty($all_records)) {
                foreach($all_records as $record) {
                    static::addToGrouped($record);
                }
            }
        }
    }

    public static function getAllByGrouped() {
        static::loadAllByGrouped();
        
        return static::$all_by_grouped;
    }
    
    public static function getByGrouped(array $group_segments) {
        static::loadAllByGrouped();
        
        $record = NULL;
        
        $group_level = static::$all_by_grouped;
        
        foreach($group_segments as $group_value) {
            if(isset($group_level[$group_value])) {
                $group_level = $group_level[$group_value];
            }
            else {
                break;
            }
        }
        
        if($group_level instanceof Model) {
            $record = $group_level;
        }
        
        return $record;
    }
    
    public static function addToGrouped(Model $record) {
        $group_fields = static::getGroupFields();
        
        if(empty($group_fields)) {
            throw new Exception("No group fields have been specified. Please override getGroupFields() and specify which fields each record will be grouped by.");
        }
        
        $group_level = &static::$all_by_grouped;
        
        foreach($group_fields as $group_field) {
            $group_value = $record->$group_field;

            if(!isset($group_level[$group_value])) {
                $group_level[$group_value] = [];
            }
            
            $group_level = &$group_level[$group_value];
        }
        
        $group_level = $record;
    }
}