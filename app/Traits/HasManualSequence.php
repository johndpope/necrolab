<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait HasManualSequence {    
    protected static $sequence_name;
    
    public static function getNewRecordId() {
        if(!isset(static::$sequence_name)) {
            static::$sequence_name = (new static())->getTable() . '_seq';
        }
    
        $new_record_id = DB::selectOne("
            SELECT nextval('" . static::$sequence_name . "'::regclass) AS id
        ");
        
        return $new_record_id->id;
    }
}