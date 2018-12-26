<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCompositePrimaryKey;

class LeaderboardSourceMultiplayerTypes extends Model {
    use HasCompositePrimaryKey;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'leaderboard_source_multiplayer_types';
    
    /**
     * The primary key associated with the model.
     *
     * @var string
     */
    protected $primaryKey = [
        'leaderboard_source_id',
        'multiplayer_type_id'
    ];
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
