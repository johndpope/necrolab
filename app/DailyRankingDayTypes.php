<?php

namespace App;

use DateTime;
use ElcoBvg\Opcache\Model;
use App\Traits\StoredInCache;
use App\Traits\GetByName;
use App\LeaderboardSources;

class DailyRankingDayTypes extends Model {
    use StoredInCache, GetByName;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'daily_ranking_day_types';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    
    public static function getStoredInCacheQuery() {
        return static::where('enabled', 1)
            ->orderBy('sort_order', 'asc');
    }
    
    public static function getAllByNameForDate(LeaderboardSources $leaderboard_source, DateTime $date): array {
        $records = static::getAllByName();
        
        if(!empty($records)) {
            foreach($records as $record) {
                $start_date = NULL;
                
                if(!empty($record->name)) {
                    $start_date = new DateTime();
                    
                    $start_date->modify("-{$record->name} day");
                }
                else {
                    $start_date = new DateTime($leaderboard_source->start_date);
                }
                
                $record->start_date = $start_date;
            }
        }
        
        return $records;
    }
}
