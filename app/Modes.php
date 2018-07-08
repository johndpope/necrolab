<?php

namespace App;

use ElcoBvg\Opcache\Model;
use App\Traits\GetByName;
use App\Traits\GetById;
use App\Traits\StoredInCache;

class Modes extends Model {
    use GetByName, GetById, StoredInCache;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'modes';
    
    /**
     * The primary key associated with the model.
     *
     * @var string
     */
    protected $primaryKey = 'mode_id';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    
    public static function getValidationRules() {
        return [
            'name' => 'required|max:100|unique:modes',
            'display_name' => 'required|max:100',
            'sort_order' => 'required|integer|min:1',
        ];
    }
    
    public static function getCacheQuery() {
        return static::orderBy('sort_order', 'asc');
    }
    
    public static function getModeFromString($string) {
        $mode = Modes::getByName('normal');
        
        if(substr_count($string, 'hard') > 1) {
            $mode = static::getByName('hard');
        }
        elseif(stripos($string, 'hard') !== false && stripos($string, 'hardcore') === false) { 
            $mode = static::getByName('hard');
        }
        
        if(stripos($string, 'no return') !== false) {            
            $mode = static::getByName('no_return');
        }
        
        if(stripos($string, 'phasing') !== false) {            
            $mode = static::getByName('phasing');
        }
        
        if(stripos($string, 'randomizer') !== false) {            
            $mode = static::getByName('randomizer');
        }
        
        if(stripos($string, 'mystery') !== false) {            
            $mode = static::getByName('mystery');
        }
        
        return $mode;
    }
}