<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Characters extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'characters';
    
    /**
     * The primary key associated with the model.
     *
     * @var string
     */
    protected $primaryKey = 'character_id';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
