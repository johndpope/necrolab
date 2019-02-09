<?php

namespace App\Traits;

use Exception;
use Illuminate\Database\Query\Builder;
use App\LeaderboardSources;

trait AddsSqlCriteria {
    public static function addSelects(Builder $query) {
        throw new Exception(static::class . '::' . __METHOD__ . 'has not been implemented.');
    }
    
    public static function addJoins(LeaderboardSources $leaderboard_source, Builder $query) {
        throw new Exception(static::class . '::' . __METHOD__ . 'has not been implemented.');
    }
    
    public static function addLeftJoins(LeaderboardSources $leaderboard_source, Builder $query) {
        throw new Exception(static::class . '::' . __METHOD__ . 'has not been implemented.');
    }
}
