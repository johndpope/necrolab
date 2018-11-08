<?php

namespace App;

use ElcoBvg\Opcache\Model;
use App\Traits\GetByName;
use App\Traits\GetById;
use App\Traits\StoredInCache;

class SeededTypes extends Model {
    use GetByName, GetById, StoredInCache;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'seeded_types';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    
    public static function getFromString($string) {
        $record = static::getByName('unseeded');

        if(stripos($string, 'seeded') !== false) {            
            $record = static::getByName('seeded');
        }
        
        return $record;
    }
}
