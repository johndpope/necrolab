<?php

namespace App;

use stdClass;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Traits\GetByName;
use App\Traits\GetById;
use App\Traits\HasManualSequence;
use App\Traits\HasTempTable;

class Seeds extends Model {
    use GetByName, GetById, HasManualSequence, HasTempTable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'seeds';
    
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
    
    /*
        This functionality was borrowed from: https://braceyourselfgames.com/forums/viewtopic.php?f=5&t=3240
        
        All credit goes to blueblimp. Thank you!
    */
    public static function getSeedFromZ1Seed(int $zone_1_seed) {
        $base = 6;
        $mul = 23987;
        $invmul = 492935547;
        $period = 2 ** 32;
        $overflow = 2 ** 32;
        
        if((($mul * $invmul) % $period) != 1) {
            throw new Exception("((mul * invmul) % period) does not equal one.");
        }
        
        $zone_1_seed -= $base;
        
        while($zone_1_seed < 0) {
            $zone_1_seed += $period;
        }
        
        $seed = ($zone_1_seed * $invmul) % $period;
        
        if($seed >= $overflow) {
            $seed -= $period;
        }

        return $seed;
    }
    
    public static function getOldDLCSeedFromZ1Seed(int $zone_1_seed) {
        $multiplied_seed = $zone_1_seed * 1899818559;
        
        $seed = $multiplied_seed % 2147483647;
        
        return $seed;
    }
    
    /*
        This functionality was borrowed from: https://github.com/necrommunity/replay-parser/blob/master/js/main.js#L5
        
        All credit goes to AlexisYJ and Grimy. Thank you!
    */
    public static function getDLCSeedFromZ1Seed(int $zone_1_seed) {
        $added_seed = $zone_1_seed + 1073765959;
        
        $multiplied_seed = $added_seed * 225371434;
        
        $modulus_seed = $multiplied_seed % 2147483647;
        
        $seed = $modulus_seed % 1899818559;
        
        return $seed;
    }
    
    public static function generateNew() {
        $new_seed = NULL;
        
        do {
            $random_seed = random_int(0, PHP_INT_MAX);
            
            $existing_record = static::where('name', $random_seed)->first();
            
            if(empty($existing_record)) {
                $new_record = new static();
                
                $new_record->id = static::getNewRecordId();
                $new_record->name = $random_seed;
                
                $new_record->save();
                
                $new_seed = $random_seed;
            }
        }
        while(!isset($new_seed));
        
        return $new_seed;
    }
    
    public static function createTemporaryTable() {
        DB::statement("
            CREATE TEMPORARY TABLE " . static::getTempTableName() . " (
                id bigint,
                name character varying(100)
            )
            ON COMMIT DROP;
        ");
    }
    
    public static function saveNewFromTemp() {
        DB::statement("
            INSERT INTO seeds (
                id,
                name
            )
            SELECT 
                id,
                name
            FROM " . static::getTempTableName() . "
        ");
    }
}
