<?php

namespace App;

use DateTime;
use Illuminate\Support\Facades\DB;
use ElcoBvg\Opcache\Builder;
use Illuminate\Database\Eloquent\Collection;
use ElcoBvg\Opcache\Model;
use App\Traits\GetByName;
use App\Traits\GetById;
use App\Traits\MatchesOnString;
use App\Traits\HasDefaultRecord;
use App\Traits\StoredInCache;
use App\ReleaseMatches;

class Releases extends Model {
    use GetByName, GetById, MatchesOnString, HasDefaultRecord, StoredInCache;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'releases';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    
    public static function getStoredInCacheQuery(): Builder {
        $modes_query = DB::table('release_modes AS rm')
            ->select([
                'rm.release_id',
                DB::raw('string_agg(m.name, \',\' ORDER BY m.sort_order) AS modes'),
            ])
            ->join('modes AS m', 'm.id', '=', 'rm.mode_id')
            ->groupBy('rm.release_id');
        
        $characters_query = DB::table('release_characters AS rc')
            ->select([
                'rc.release_id',
                DB::raw('string_agg(c.name, \',\' ORDER BY c.sort_order) AS characters')
            ])
            ->join('characters AS c', 'c.id', '=', 'rc.character_id')
            ->groupBy('rc.release_id');
    
        return static::select([
            'r.id',
            'r.name',
            'r.display_name',
            'r.start_date',
            'r.end_date',
            'r.is_default',
            'release_modes.modes',
            'release_characters.characters'
        ])
            ->from('releases AS r')
            ->leftJoinSub($modes_query, 'release_modes', function($join) {
                $join->on('release_modes.release_id', '=', 'r.id');
            })
            ->leftJoinSub($characters_query, 'release_characters', function($join) {
                $join->on('release_characters.release_id', '=', 'r.id');
            })
            ->orderBy('r.sort_order', 'asc');
    }
    
    protected static function processDataBeforeCache(Collection $records): void {    
        if(!empty($records)) {
            foreach($records as $record) {                
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
        return ReleaseMatches::class;
    }
    
    protected static function getMatchFieldIdName(): string {
        return 'release_id';
    }
    
    public static function getAllByDate(DateTime $date): array {
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
