<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SteamUsers extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'steam_users';
    
    /**
     * The primary key associated with the model.
     *
     * @var string
     */
    protected $primaryKey = 'steam_user_id';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
