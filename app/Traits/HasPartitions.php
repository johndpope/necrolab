<?php

namespace App\Traits;

use DateTime;
use DateInterval;

trait HasPartitions {
    protected static $base_table_name;
    
    public static function loadBaseTableName() {
        if(!isset(static::$base_table_name)) {
            static::$base_table_name = (new static())->getTable();
        }
    }
    
    public static function getBaseTableName() {
        static::loadBaseTableName();
        
        return static::$base_table_name;
    }

    public static function getTableName(DateTime $date) {
        $base_name = static::getBaseTableName();
    
        return "{$base_name}_{$date->format('Y_m')}";
    }

    public static function getTableNames(DateTime $start_date, DateTime $end_date) {
        $table_names = [];
        
        $current_date = new DateTime($start_date->format('Y-m-01'));
        
        while($current_date <= $end_date) {
            $table_names[] = static::getTableName($current_date);
        
            $current_date->add(new DateInterval('P1M'));
        }
        
        return $table_names;
    }
}