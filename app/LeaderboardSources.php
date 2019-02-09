<?php

namespace App;

use Illuminate\Support\Facades\DB;
use ElcoBvg\Opcache\Builder;
use Illuminate\Database\Eloquent\Collection;
use ElcoBvg\Opcache\Model;
use App\Traits\GetByName;
use App\Traits\GetById;
use App\Traits\StoredInCache;

class LeaderboardSources extends Model {
    use GetByName, GetById, StoredInCache;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'leaderboard_sources';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    
    protected static function getStoredInCacheQuery(): Builder {
        $releases_query = DB::table('leaderboard_source_releases AS lsr')
            ->select([
                'lsr.leaderboard_source_id',
                DB::raw('string_agg(r.name, \',\' ORDER BY r.start_date) AS releases')
            ])
            ->join('releases AS r', 'r.id', '=', 'lsr.release_id')
            ->groupBy('lsr.leaderboard_source_id');
    
        $characters_query = DB::table('leaderboard_source_characters AS lsc')
            ->select([
                'lsc.leaderboard_source_id',
                DB::raw('string_agg(c.name, \',\' ORDER BY c.sort_order) AS characters')
            ])
            ->join('characters AS c', 'c.id', '=', 'lsc.character_id')
            ->groupBy('lsc.leaderboard_source_id');
            
        $multiplayer_types_query = DB::table('leaderboard_source_multiplayer_types AS lsmt')
            ->select([
                'lsmt.leaderboard_source_id',
                DB::raw('string_agg(mt.name, \',\' ORDER BY mt.id) AS multiplayer_types')
            ])
            ->join('multiplayer_types AS mt', 'mt.id', '=', 'lsmt.multiplayer_type_id')
            ->groupBy('lsmt.leaderboard_source_id');
        
        return static::select([
            'ls.id',
            'ls.name',
            'ls.display_name',
            'leaderboard_source_releases.releases',
            'leaderboard_source_characters.characters',
            'leaderboard_source_multiplayer_types.multiplayer_types'
        ])
            ->from('leaderboard_sources AS ls')
            ->leftJoinSub($releases_query, 'leaderboard_source_releases', function($join) {
                $join->on('leaderboard_source_releases.leaderboard_source_id', '=', 'ls.id');
            })
            ->leftJoinSub($characters_query, 'leaderboard_source_characters', function($join) {
                $join->on('leaderboard_source_characters.leaderboard_source_id', '=', 'ls.id');
            })
            ->leftJoinSub($multiplayer_types_query, 'leaderboard_source_multiplayer_types', function($join) {
                $join->on('leaderboard_source_multiplayer_types.leaderboard_source_id', '=', 'ls.id');
            })
            ->orderBy('ls.sort_order', 'asc');
    }
    
    protected static function processDataBeforeCache(Collection $records): void {    
        if(!empty($records)) {
            foreach($records as $record) {
                /* ---------- Releases ---------- */ 
            
                $releases = explode(',', $record->releases);
                
                if(empty($releases)) {
                    $releases = [];
                }
                
                $record->releases = $releases;
                
                
                /* ---------- Characters ---------- */ 
            
                $characters = explode(',', $record->characters);
                
                if(empty($characters)) {
                    $characters = [];
                }
                
                $record->characters = $characters;
                
                
                /* ---------- Multiplayer Types ---------- */ 
            
                $multiplayer_types = explode(',', $record->multiplayer_types);
                
                if(empty($multiplayer_types)) {
                    $multiplayer_types = [];
                }
                
                $record->multiplayer_types = $multiplayer_types;
            }
        }
    }
}
