<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PowerRankings extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'power_rankings';
    
    /**
     * The primary key associated with the model.
     *
     * @var string
     */
    protected $primaryKey = 'power_ranking_id';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
