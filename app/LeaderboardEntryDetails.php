<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeaderboardEntryDetails extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'leaderboard_entry_details';
    
    /**
     * The primary key associated with the model.
     *
     * @var string
     */
    protected $primaryKey = 'leaderboard_entry_details_id';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
