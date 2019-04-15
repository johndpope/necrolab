<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RedditUsers extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'reddit_users';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
