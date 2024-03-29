<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TwitterUserTokens extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'twitter_user_tokens';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
