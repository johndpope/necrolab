<?php

namespace App\Components\CacheNames\Users;

use App\Components\CacheNames\Core;

class Steam
extends Core {    
    protected const STEAM_USERS = 'su';
    
    protected const ALL_RECORDS = 'records';
    
    protected const STEAM_USERS_BY_NAME = 'names';
    
    protected const PBS = 'pbs';

    public static function getBase() {
        return self::STEAM_USERS;
    }
    
    public static function getIds() {
        return self::getBase() . ':'  . static::IDS;
    }
    
    public static function getAllRecords() {
        return self::getBase() . ':'  . self::ALL_RECORDS;
    }
    
    public static function getUsersByName() {
        return self::getBase() . ':' . self::STEAM_USERS_BY_NAME;
    }
    
    public static function getUsersIndex(array $index_segments = array()) {                
        return parent::getIndex(self::getBase() . ':' . static::INDEX, $index_segments);
    }
    
    public static function getAllPbs() {
        return self::getBase() . ':'  . self::PBS;
    }
    
    public static function getPbsIndex(array $index_segments = array()) {
        return parent::getIndex(self::getAllPbs() . ':' . static::INDEX, $index_segments);
    }
}
