<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCompositePrimaryKey;

class LeaderboardSourceReleases extends Model {
    use HasCompositePrimaryKey;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'leaderboard_source_releases';
    
    /**
     * The primary key associated with the model.
     *
     * @var string
     */
    protected $primaryKey = [
        'leaderboard_source_id',
        'release_id'
    ];
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
