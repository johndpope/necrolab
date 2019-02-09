<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\IsSchemaTable;
use App\Traits\GetById;
use App\Traits\GetByName;
use App\Traits\StoredInCache;

class Achievements extends Model {
    use IsSchemaTable, GetById, GetByName, StoredInCache;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'achievements';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
