<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RunResults extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'run_results';
    
    /**
     * The primary key associated with the model.
     *
     * @var string
     */
    protected $primaryKey = 'run_result_id';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
