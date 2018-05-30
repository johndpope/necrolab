<?php

namespace App;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use App\Traits\GetByName;
use App\Traits\GetById;

class Releases extends Model {
    use GetByName, GetById;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'releases';
    
    /**
     * The primary key associated with the model.
     *
     * @var string
     */
    protected $primaryKey = 'release_id';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    
    public static function getEarliestStartDate(array $releases) {
        $earliest_start_date = NULL;
        
        if(!empty($releases)) {
            foreach($releases as $release) {
                $start_date = new DateTime($release['start_date']);
                
                if(empty($earliest_start_date) || $start_date < $earliest_start_date) {
                    $earliest_start_date = $start_date;
                }
            }
        }
        
        return $earliest_start_date;
    }
    
    public static function getAllByDate(DateTime $date) {
        $releases = static::all();
        
        $release_records = [];
        
        if(!empty($releases)) {        
            foreach($releases as $release) {
                $start_date = new DateTime($release->start_date);
                $end_date = new DateTime($release->end_date);
            
                if($date >= $start_date && $date <= $end_date) {
                    $release_records[] = $release;
                }
            }
        }
        
        return $release_records;
    }
    
    public static function getReleaseFromString($string) {
        $release = NULL;
    
        if(stripos($string, 'dev') !== false) {
            $release = static::getByName('early_access');
        }
        elseif(stripos($string, 'prod') !== false) {
            if(stripos($string, 'dlc') !== false) {
                $release = static::getByName('amplified_dlc');
            }
            else {
                $release = static::getByName('original');
            }
        }
        
        return $release;
    }
}