<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\GetByName;

class RankingTypes extends Model {
    use GetByName;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ranking_types';
    
    /**
     * The primary key associated with the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
