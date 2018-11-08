<?php

namespace App;

use ElcoBvg\Opcache\Model;
use App\Traits\GetByName;
use App\Traits\GetById;
use App\Traits\StoredInCache;

class MultiplayerTypes extends Model {
    use GetByName, GetById, StoredInCache;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'multiplayer_types';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    
    public static function getFromString($string) {
        $record = static::getByName('single');

        if(stripos($string, 'co-op') !== false) {            
            $record = static::getByName('co_op');
        }
        
        return $record;
    }
}
