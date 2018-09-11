<?php

namespace App\Traits;

use Exception;
use Illuminate\Database\Query\Builder;

trait AddsSqlCriteria {
    public static function addSelects(Builder $query) {
        throw new Exception(static::class . '::' . __METHOD__ . 'has not been implemented.');
    }
    
    public static function addJoins(Builder $query) {
        throw new Exception(static::class . '::' . __METHOD__ . 'has not been implemented.');
    }
    
    public static function addLeftJoins(Builder $query) {
        throw new Exception(static::class . '::' . __METHOD__ . 'has not been implemented.');
    }
}
