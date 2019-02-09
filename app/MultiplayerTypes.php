<?php

namespace App;

use ElcoBvg\Opcache\Model;
use App\Traits\GetByName;
use App\Traits\GetById;
use App\Traits\MatchesOnString;
use App\Traits\HasDefaultRecord;
use App\Traits\StoredInCache;
use App\MultiplayerTypeMatches;

class MultiplayerTypes extends Model {
    use GetByName, GetById, MatchesOnString, HasDefaultRecord, StoredInCache;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'multiplayer_types';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    
    protected static function getMatchModel(): string {
        return MultiplayerTypeMatches::class;
    }
    
    protected static function getMatchFieldIdName(): string {
        return 'multiplayer_type_id';
    }
}
