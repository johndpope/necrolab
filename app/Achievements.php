<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Achievements extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'achievements';
    
    /**
     * The primary key associated with the model.
     *
     * @var string
     */
    protected $primaryKey = 'achievement_id';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
