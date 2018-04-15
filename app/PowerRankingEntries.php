<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PowerRankingEntries extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'modes';
    
    /**
     * The primary key associated with the model.
     *
     * @var string
     */
    protected $primaryKey = [
        'power_ranking_id',
        'steam_user_id'
    ];
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
