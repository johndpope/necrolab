<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class YoutubeUsers extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'youtube_users';
    
    /**
     * The primary key associated with the model.
     *
     * @var string
     */
    protected $primaryKey = 'youtube_user_id';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
