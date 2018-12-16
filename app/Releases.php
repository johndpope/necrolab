<?php

namespace App;

use DateTime;
use Illuminate\Support\Facades\DB;
use ElcoBvg\Opcache\Model;
use App\Traits\GetByName;
use App\Traits\GetById;
use App\Traits\StoredInCache;

class Releases extends Model {
    use GetByName, GetById, StoredInCache;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'releases';
    
    /**
     * The primary key associated with the model.
     *
     * @var string
     */
    protected $primaryKey = 'release_id';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    
    public static function getStoredInCacheQuery() {
        $modes_query = DB::table('release_modes AS rm')
            ->select([
                'rm.release_id',
                DB::raw('string_agg(m.name, \',\' ORDER BY m.sort_order) AS modes'),
            ])
            ->join('modes AS m', 'm.mode_id', '=', 'rm.mode_id')
            ->groupBy('rm.release_id');
        
        $characters_query = DB::table('release_characters AS rc')
            ->select([
                'rc.release_id',
                DB::raw('string_agg(c.name, \',\' ORDER BY c.sort_order) AS characters')
            ])
            ->join('characters AS c', 'c.character_id', '=', 'rc.character_id')
            ->groupBy('rc.release_id');
    
        return static::select([
            'r.release_id',
            'r.name',
            'r.display_name',
            'r.start_date',
            'r.end_date',
            'release_modes.modes',
            'release_characters.characters'
        ])
            ->from('releases AS r')
            ->leftJoinSub($modes_query, 'release_modes', function($join) {
                $join->on('release_modes.release_id', '=', 'r.release_id');
            })
            ->leftJoinSub($characters_query, 'release_characters', function($join) {
                $join->on('release_characters.release_id', '=', 'r.release_id');
            })
            ->groupBy([
                'r.release_id',
                'r.name',
                'r.display_name',
                'r.start_date',
                'r.end_date',
                'release_modes.modes',
                'release_characters.characters'
            ])
            ->orderBy('r.start_date', 'asc');
    }
    
    public static function getEarliestStartDate(array $releases) {
        $earliest_start_date = NULL;
        
        if(!empty($releases)) {
            foreach($releases as $release) {
                $start_date = new DateTime($release['start_date']);
                
                if(empty($earliest_start_date) || $start_date < $earliest_start_date) {
                    $earliest_start_date = $start_date;
                }
            }
        }
        
        return $earliest_start_date;
    }
    
    public static function getAllByDate(DateTime $date) {
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
    
    public static function getReleaseFromString($string) {
        $release = NULL;
    
        if(stripos($string, 'dev') !== false) {
            $release = static::getByName('early_access');
        }
        elseif(stripos($string, 'prod') !== false) {
            if(stripos($string, 'dlc') !== false) {
                $release = static::getByName('amplified_dlc');
            }
            else {
                $release = static::getByName('original');
            }
        }
        
        return $release;
    }
}
