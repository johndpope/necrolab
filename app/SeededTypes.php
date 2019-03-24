<?php

namespace App;

use ElcoBvg\Opcache\Builder;
use ElcoBvg\Opcache\Model;
use App\Traits\GetByName;
use App\Traits\GetById;
use App\Traits\MatchesOnString;
use App\Traits\HasDefaultRecord;
use App\Traits\StoredInCache;
use App\SeededTypeMatches;

class SeededTypes extends Model {
    use GetByName, GetById, MatchesOnString, HasDefaultRecord, StoredInCache;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'seeded_types';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    
    protected static function getStoredInCacheQuery(): Builder {
        return static::orderBy('sort_order', 'asc');
    }
    
    protected static function getMatchModel(): string {
        return SeededTypeMatches::class;
    }
    
    protected static function getMatchFieldIdName(): string {
        return 'seeded_type_id';
    }
}
