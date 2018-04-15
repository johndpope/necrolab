<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SteamUserAchievements extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'steam_user_achievements';
    
    /**
     * The primary key associated with the model.
     *
     * @var string
     */
    protected $primaryKey = [
        'steam_user_id',
        'achievement_id'
    ];
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
