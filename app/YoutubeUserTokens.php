<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class YoutubeUserTokens extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'youtube_user_tokens';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
