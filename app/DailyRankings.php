<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DailyRankings extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'daily_rankings';
    
    /**
     * The primary key associated with the model.
     *
     * @var string
     */
    protected $primaryKey = 'daily_ranking_id';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
