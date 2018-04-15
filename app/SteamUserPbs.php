<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SteamUserPbs extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'steam_user_pbs';
    
    /**
     * The primary key associated with the model.
     *
     * @var string
     */
    protected $primaryKey = 'steam_user_pb_id';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
