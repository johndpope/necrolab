<?php

namespace App;

use Illuminate\Support\Facades\DB;
use ElcoBvg\Opcache\Model;
use App\Traits\GetByName;
use App\Traits\GetById;
use App\Traits\StoredInCache;

class DataTypes extends Model {
    use GetByName, GetById, StoredInCache;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'data_types';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
