<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dates extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'dates';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['date'];
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
