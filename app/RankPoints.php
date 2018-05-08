<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RankPoints extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rank_points';
    
    /**
     * The primary key associated with the model.
     *
     * @var string
     */
    protected $primaryKey = 'rank';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    
    public static function calculateFromRank($rank) {
        return 1.7 / (log($rank / 100 + 1.03) / log(10));
    }
}