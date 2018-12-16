<?php

namespace App;

use Illuminate\Support\Facades\DB;
use ElcoBvg\Opcache\Model;
use App\Traits\GetByName;
use App\Traits\GetById;
use App\Traits\StoredInCache;

class LeaderboardDetailsColumns extends Model {
    use GetByName, GetById, StoredInCache;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'leaderboard_details_columns';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    
    protected static function getStoredInCacheQuery() {        
        return static::select([
            'ldc.id',
            'ldc.name',
            'ldc.display_name',
            'dt.name AS data_type'
        ])
            ->from('leaderboard_details_columns AS ldc')
            ->join('data_types AS dt', 'dt.id', '=', 'ldc.data_type_id')
            ->where('ldc.enabled', 1)
            ->orderBy('ldc.sort_order', 'asc');
    }
}
