<?php

namespace App;

use Illuminate\Support\Facades\DB;
use ElcoBvg\Opcache\Model;
use App\Traits\GetById;
use App\Traits\GetByName;
use App\Traits\StoredInCache;

class LeaderboardTypes extends Model {
    use GetById, GetByName, StoredInCache;

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
    
    public static function getStoredInCacheQuery() {
        $modes_query = DB::table('leaderboard_type_modes AS ltm')
            ->select([
                'ltm.leaderboard_type_id',
                DB::raw('string_agg(m.name, \',\' ORDER BY m.mode_id) AS modes')
            ])
            ->join('modes AS m', 'm.mode_id', '=', 'ltm.mode_id')
            ->groupBy('ltm.leaderboard_type_id');
    
        $characters_query = DB::table('leaderboard_type_characters AS ltc')
            ->select([
                'ltc.leaderboard_type_id',
                DB::raw('string_agg(c.name, \',\' ORDER BY c.sort_order) AS characters')
            ])
            ->join('characters AS c', 'c.character_id', '=', 'ltc.character_id')
            ->groupBy('ltc.leaderboard_type_id');
        
        return static::select([
            'lt.leaderboard_type_id',
            'lt.name',
            'lt.display_name',
            'ldc.name AS details_column',
            'leaderboard_type_modes.modes',
            'leaderboard_type_characters.characters'
        ])
            ->from('leaderboard_types AS lt')
            ->join('leaderboard_details_columns AS ldc', 'ldc.id', 'lt.leaderboard_details_column_id')
            ->leftJoinSub($modes_query, 'leaderboard_type_modes', function($join) {
                $join->on('leaderboard_type_modes.leaderboard_type_id', '=', 'lt.leaderboard_type_id');
            })
            ->leftJoinSub($characters_query, 'leaderboard_type_characters', function($join) {
                $join->on('leaderboard_type_characters.leaderboard_type_id', '=', 'lt.leaderboard_type_id');
            })
            ->groupBy([
                'lt.leaderboard_type_id',
                'lt.name',
                'lt.display_name',
                'ldc.name',
                'leaderboard_type_modes.modes',
                'leaderboard_type_characters.characters'
            ])
            ->orderBy('lt.leaderboard_type_id', 'asc');
    }
    
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
