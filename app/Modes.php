<?php

namespace App;

use Illuminate\Support\Facades\DB;
use ElcoBvg\Opcache\Builder;
use Illuminate\Database\Eloquent\Collection;
use ElcoBvg\Opcache\Model;
use App\Traits\GetByName;
use App\Traits\GetById;
use App\Traits\MatchesOnString;
use App\Traits\HasDefaultRecord;
use App\Traits\StoredInCache;
use App\ModeMatches;

class Modes extends Model {
    use GetByName, GetById, MatchesOnString, HasDefaultRecord, StoredInCache;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'modes';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    
    public static function getStoredInCacheQuery(): Builder {
        $leaderboard_types_query = DB::table('leaderboard_type_modes AS ltm')
            ->select([
                'ltm.mode_id',
                DB::raw('string_agg(lt.name, \',\' ORDER BY lt.id) AS leaderboard_types')
            ])
            ->join('leaderboard_types AS lt', 'lt.id', '=', 'ltm.leaderboard_type_id')
            ->groupBy('ltm.mode_id');
    
        $characters_query = DB::table('mode_characters AS mc')
            ->select([
                'mc.mode_id',
                DB::raw('string_agg(c.name, \',\' ORDER BY c.sort_order) AS characters')
            ])
            ->join('characters AS c', 'c.id', '=', 'mc.character_id')
            ->groupBy('mc.mode_id');
    
        return static::select([
            'm.id',
            'm.name',
            'm.display_name',
            'm.is_default',
            'leaderboard_types',
            'mode_leaderboard_types.leaderboard_types',
            'mode_characters.characters'
        ])
            ->from('modes AS m')
            ->leftJoinSub($leaderboard_types_query, 'mode_leaderboard_types', function($join) {
                $join->on('mode_leaderboard_types.mode_id', '=', 'm.id');
            })
            ->leftJoinSub($characters_query, 'mode_characters', function($join) {
                $join->on('mode_characters.mode_id', '=', 'm.id');
            })
            ->orderBy('m.sort_order', 'asc');
    }
    
    protected static function processDataBeforeCache(Collection $records): void {    
        if(!empty($records)) {
            foreach($records as $record) {                
                /* ---------- Leaderboard types ---------- */ 
            
                $leaderboard_types = explode(',', $record->leaderboard_types);
                
                if(empty($leaderboard_types)) {
                    $leaderboard_types = [];
                }
                
                $record->leaderboard_types = $leaderboard_types;
                
                
                /* ---------- Characters ---------- */ 
            
                $characters = explode(',', $record->characters);
                
                if(empty($characters)) {
                    $characters = [];
                }
                
                $record->characters = $characters;
            }
        }
    }
    
    protected static function getMatchModel(): string {
        return ModeMatches::class;
    }
    
    protected static function getMatchFieldIdName(): string {
        return 'mode_id';
    }
}
