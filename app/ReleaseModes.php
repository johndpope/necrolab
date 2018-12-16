<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCompositePrimaryKey;

class ReleaseModes extends Model {
    use HasCompositePrimaryKey;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'release_modes';
    
    /**
     * The primary key associated with the model.
     *
     * @var string
     */
    protected $primaryKey = [
        'release_id',
        'mode_id'
    ];
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
