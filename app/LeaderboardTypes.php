<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\GetByName;

class LeaderboardTypes extends Model {
    use GetByName;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'leaderboard_types';
    
    /**
     * The primary key associated with the model.
     *
     * @var string
     */
    protected $primaryKey = 'leaderboard_type_id';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    
    public static function getTypeFromString($string) {
        $leaderboard_type = NULL;
    
        if(stripos($string, 'speedrun') !== false) {            
            $leaderboard_type = static::getByName('speed');
        }
        
        if(stripos($string, 'hardcore') !== false || stripos($string, 'core') !== false || stripos($string, 'all zones') !== false) {            
            $leaderboard_type = static::getByName('score');
        }
        
        if(stripos($string, 'deathless') !== false) {            
            $leaderboard_type = static::getByName('deathless');
        }
        
        return $leaderboard_type;
    }
}
