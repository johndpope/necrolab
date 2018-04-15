<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DailyRankingEntries extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'daily_ranking_entries';
    
    /**
     * This table has a composite primary key.
     *
     * @var string
     */
    protected $primaryKey = [
        'daily_ranking_id',
        'steam_user_id'
    ];
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}