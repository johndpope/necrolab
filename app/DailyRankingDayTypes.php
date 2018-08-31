<?php

namespace App;

use DateTime;
use DateInterval;
use ElcoBvg\Opcache\Model;
use App\Traits\StoredInCache;
use App\Traits\GetByName;

class DailyRankingDayTypes extends Model {
    use StoredInCache, GetByName;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'daily_ranking_day_types';
    
    /**
     * The primary key associated with the model.
     *
     * @var string
     */
    protected $primaryKey = 'daily_ranking_day_type_id';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    
    public static function getStoredInCacheQuery() {
        return static::where('enabled', 1)
            ->orderBy('name', 'asc');
    }
    
    public static function getAllActiveForDate(DateTime $date) {
        $all_records = static::where('enabled', 1)->get();
        
        $active_day_types = array();
        
        if(!empty($all_records)) {
            $steam_live_launch_date = new DateTime(env('STEAM_LIVE_LAUNCH_DATE'));
        
            foreach($all_records as $active_day_type) {
                $number_of_days = $active_day_type->name;
                
                if($number_of_days == 0) {
                    $number_of_days = $date->diff($steam_live_launch_date)->format('%a');
                }
            
                $day_type_start_date = clone $date;
                
                $day_type_start_date->sub(new DateInterval("P{$number_of_days}D"));
                
                $active_day_type->start_date = new DateTime($day_type_start_date->format('Y-m-d'));
            
                $active_day_types[$active_day_type->daily_ranking_day_type_id] = $active_day_type;
            }
        }
        
        return $active_day_types; 
    }
}
