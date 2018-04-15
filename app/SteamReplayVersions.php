<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SteamReplayVersions extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'steam_replay_versions';
    
    /**
     * The primary key associated with the model.
     *
     * @var string
     */
    protected $primaryKey = 'steam_replay_version_id';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
