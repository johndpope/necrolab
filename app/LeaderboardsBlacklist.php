<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeaderboardsBlacklist extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'leaderboards_blacklist';
    
    /**
     * The primary key associated with the model.
     *
     * @var string
     */
    protected $primaryKey = 'leaderboards_blacklist_id';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
