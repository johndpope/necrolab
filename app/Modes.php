<?php

namespace App;

use Illuminate\Support\Facades\DB;
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
    
    public static function getStoredInCacheQuery() {
        $leaderboard_types_query = DB::table('leaderboard_type_modes AS ltm')
            ->select([
                'ltm.mode_id',
                DB::raw('string_agg(lt.name, \',\' ORDER BY lt.leaderboard_type_id) AS leaderboard_types')
            ])
            ->join('leaderboard_types AS lt', 'lt.leaderboard_type_id', '=', 'ltm.leaderboard_type_id')
            ->groupBy('ltm.mode_id');
    
        $characters_query = DB::table('mode_characters AS mc')
            ->select([
                'mc.mode_id',
                DB::raw('string_agg(c.name, \',\' ORDER BY c.sort_order) AS characters')
            ])
            ->join('characters AS c', 'c.character_id', '=', 'mc.character_id')
            ->groupBy('mc.mode_id');
    
        return static::select([
            'm.mode_id',
            'm.name',
            'm.display_name',
            'leaderboard_types',
            'mode_leaderboard_types.leaderboard_types',
            'mode_characters.characters'
        ])
            ->from('modes AS m')
            ->leftJoinSub($leaderboard_types_query, 'mode_leaderboard_types', function($join) {
                $join->on('mode_leaderboard_types.mode_id', '=', 'm.mode_id');
            })
            ->leftJoinSub($characters_query, 'mode_characters', function($join) {
                $join->on('mode_characters.mode_id', '=', 'm.mode_id');
            })
            ->groupBy([
                'm.mode_id',
                'm.name',
                'm.display_name',
                'mode_leaderboard_types.leaderboard_types',
                'mode_characters.characters'
            ])
            ->orderBy('m.sort_order', 'asc');
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
