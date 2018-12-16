<?php

namespace App;

use Illuminate\Support\Facades\DB;
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
    
    protected static function getStoredInCacheQuery() {
        $releases_query = DB::table('leaderboard_source_releases AS lsr')
            ->select([
                'lsr.leaderboard_source_id',
                DB::raw('string_agg(r.name, \',\' ORDER BY r.start_date) AS releases')
            ])
            ->join('releases AS r', 'r.release_id', '=', 'lsr.release_id')
            ->groupBy('lsr.leaderboard_source_id');
    
        $characters_query = DB::table('leaderboard_source_characters AS lsc')
            ->select([
                'lsc.leaderboard_source_id',
                DB::raw('string_agg(c.name, \',\' ORDER BY c.sort_order) AS characters')
            ])
            ->join('characters AS c', 'c.character_id', '=', 'lsc.character_id')
            ->groupBy('lsc.leaderboard_source_id');
        
        return static::select([
            'ls.id',
            'ls.name',
            'ls.display_name',
            'leaderboard_source_releases.releases',
            'leaderboard_source_characters.characters'
        ])
            ->from('leaderboard_sources AS ls')
            ->leftJoinSub($releases_query, 'leaderboard_source_releases', function($join) {
                $join->on('leaderboard_source_releases.leaderboard_source_id', '=', 'ls.id');
            })
            ->leftJoinSub($characters_query, 'leaderboard_source_characters', function($join) {
                $join->on('leaderboard_source_characters.leaderboard_source_id', '=', 'ls.id');
            })
            ->groupBy([
                'ls.id',
                'ls.name',
                'ls.display_name',
                'leaderboard_source_releases.releases',
                'leaderboard_source_characters.characters'
            ])
            ->orderBy('ls.sort_order', 'asc');
    }
}
