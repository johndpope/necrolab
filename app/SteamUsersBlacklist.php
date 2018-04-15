<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SteamUsersBlacklist extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'steam_users_blacklist';
    
    /**
     * The primary key associated with the model.
     *
     * @var string
     */
    protected $primaryKey = 'steam_users_blacklist_id';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
