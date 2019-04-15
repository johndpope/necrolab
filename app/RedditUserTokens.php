<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RedditUserTokens extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'reddit_user_tokens';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
