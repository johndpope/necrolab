<?php

namespace App;

use Illuminate\Support\Facades\DB;
use ElcoBvg\Opcache\Builder;
use Illuminate\Database\Eloquent\Collection;
use ElcoBvg\Opcache\Model;
use App\Traits\GetById;
use App\Traits\GetByName;
use App\Traits\MatchesOnString;
use App\Traits\HasDefaultRecord;
use App\Traits\StoredInCache;
use App\LeaderboardTypeMatches;

class LeaderboardTypes extends Model {
    use GetById, GetByName, MatchesOnString, HasDefaultRecord, StoredInCache;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'leaderboard_types';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    
    public static function getStoredInCacheQuery(): Builder {
        $details_columns_query = DB::table('leaderboard_type_details_columns AS ltdc')
            ->select([
                'ltdc.leaderboard_type_id',
                DB::raw('string_agg(ldc.name, \',\' ORDER BY ldc.sort_order) AS details_columns')
            ])
            ->join('leaderboard_details_columns AS ldc', 'ldc.id', '=', 'ltdc.leaderboard_details_column_id')
            ->groupBy('ltdc.leaderboard_type_id');
    
        $modes_query = DB::table('leaderboard_type_modes AS ltm')
            ->select([
                'ltm.leaderboard_type_id',
                DB::raw('string_agg(m.name, \',\' ORDER BY m.id) AS modes')
            ])
            ->join('modes AS m', 'm.id', '=', 'ltm.mode_id')
            ->groupBy('ltm.leaderboard_type_id');
    
        $characters_query = DB::table('leaderboard_type_characters AS ltc')
            ->select([
                'ltc.leaderboard_type_id',
                DB::raw('string_agg(c.name, \',\' ORDER BY c.sort_order) AS characters')
            ])
            ->join('characters AS c', 'c.id', '=', 'ltc.character_id')
            ->groupBy('ltc.leaderboard_type_id');
        
        return static::select([
            'lt.id',
            'lt.name',
            'lt.display_name',
            'lt.show_seed',
            'lt.show_replay',
            'lt.show_zone_level',
            'lt.is_default',
            'details_columns.details_columns',
            'leaderboard_type_modes.modes',
            'leaderboard_type_characters.characters'
        ])
            ->from('leaderboard_types AS lt')
            ->leftJoinSub($details_columns_query, 'details_columns', function($join) {
                $join->on('details_columns.leaderboard_type_id', '=', 'lt.id');
            })
            ->leftJoinSub($modes_query, 'leaderboard_type_modes', function($join) {
                $join->on('leaderboard_type_modes.leaderboard_type_id', '=', 'lt.id');
            })
            ->leftJoinSub($characters_query, 'leaderboard_type_characters', function($join) {
                $join->on('leaderboard_type_characters.leaderboard_type_id', '=', 'lt.id');
            })
            ->orderBy('lt.sort_order', 'asc');
    }
    
    protected static function processDataBeforeCache(Collection $records): void {    
        if(!empty($records)) {
            foreach($records as $record) {
                /* ---------- Details Columns ---------- */ 
            
                $details_columns = explode(',', $record->details_columns);
                
                if(empty($details_columns)) {
                    $details_columns = [];
                }
                
                $record->details_columns = $details_columns;
                
                
                /* ---------- Modes ---------- */ 
            
                $modes = explode(',', $record->modes);
                
                if(empty($modes)) {
                    $modes = [];
                }
                
                $record->modes = $modes;
                
                
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
        return LeaderboardTypeMatches::class;
    }
    
    protected static function getMatchFieldIdName(): string {
        return 'leaderboard_type_id';
    }
}
