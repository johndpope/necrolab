<?php

namespace App;

use DateTime;
use Illuminate\Database\Eloquent\Model;

class Releases extends Model {
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
}