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
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
