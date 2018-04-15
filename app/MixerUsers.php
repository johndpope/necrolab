<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MixerUsers extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mixer_users';
    
    /**
     * The primary key associated with the model.
     *
     * @var string
     */
    protected $primaryKey = 'mixer_user_id';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
