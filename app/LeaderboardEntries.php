<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeaderboardEntries extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'leaderboard_entries';
    
    /**
     * The primary key associated with the model.
     *
     * @var string
     */
    protected $primaryKey = [
        'leaderboard_snapshot_id',
        'steam_user_pb_id',
        'rank'
    ];
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
