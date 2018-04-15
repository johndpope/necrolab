<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DailyRankingDayTypes extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'daily_ranking_day_types';
    
    /**
     * The primary key associated with the model.
     *
     * @var string
     */
    protected $primaryKey = 'daily_ranking_day_type_id';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
