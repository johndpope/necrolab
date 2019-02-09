<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\IsSchemaTable;

class LeaderboardsBlacklist extends Model {
    use IsSchemaTable;

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
    protected $primaryKey = 'leaderboard_id';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;
}
