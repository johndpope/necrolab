<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Releases extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'releases';
    
    /**
     * The primary key associated with the model.
     *
     * @var string
     */
    protected $primaryKey = 'release_id';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
