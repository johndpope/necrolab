<?php

namespace App;

use ElcoBvg\Opcache\Model;
use App\Traits\GetByName;
use App\Traits\GetById;
use App\Traits\StoredInCache;

class Soundtracks extends Model {
    use GetByName, GetById, StoredInCache;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'soundtracks';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    
    public static function getFromString($string) {
        $record = static::getByName('default');

        if(stripos($string, 'custom') !== false) {            
            $record = static::getByName('custom');
        }
        
        return $record;
    }
}
