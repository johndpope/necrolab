<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DiscordUsers extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'discord_users';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
