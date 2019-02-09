<?php

namespace App;

use ElcoBvg\Opcache\Builder;
use ElcoBvg\Opcache\Model;
use App\Traits\GetById;
use App\Traits\GetByName;
use App\Traits\MatchesOnString;
use App\Traits\HasDefaultRecord;
use App\Traits\StoredInCache;
use App\CharacterMatches;

class Characters extends Model {
    use GetById, GetByName, MatchesOnString, HasDefaultRecord, StoredInCache;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'characters';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    
    public static function getStoredInCacheQuery(): Builder {
        return static::where('is_active', 1)
            ->orderBy('sort_order', 'asc');
    }
    
    protected static function getMatchModel(): string {
        return CharacterMatches::class;
    }
    
    protected static function getMatchFieldIdName(): string {
        return 'character_id';
    }
    
    public static function isCoOpCharacter($character_name) {
        $co_op_characters = array(
            "cadence",
            "bard",
            "aria",
            "bolt",
            "monk",
            "dove",
            "eli",
            "melody",
            "dorian",
            "coda",
            'nocturna',
            'diamond',
            'mary',
            'tempo'
        );
        
        return in_array($character_name, $co_op_characters, true);
    }
    
    public static function isSeededCharacter($character_name) {
        $seeded_characters = array(
            "cadence",
            "bard",
            "aria",
            "bolt",
            "monk",
            "dove",
            "eli",
            "melody",
            "dorian",
            "coda",
            'nocturna',
            'diamond',
            'mary',
            'tempo'
        );
        
        return in_array($character_name, $seeded_characters, true);
    }
    
    public static function isDeathlessCharacter($character_name) {
        $deathless_characters = array(
            "cadence",
            "bard",
            "aria",
            "bolt",
            "monk",
            "dove",
            "eli",
            "melody",
            "dorian",
            "coda",
            'nocturna',
            'diamond',
            'mary',
            'tempo'
        );
        
        return in_array($character_name, $deathless_characters, true);
    }
    
    public static function isModeCharacter($character_name) {
        $mode_characters = array(
            "cadence",
            "bard",
            "aria",
            "bolt",
            "monk",
            "dove",
            "eli",
            "melody",
            "dorian",
            "coda",
            'nocturna',
            'diamond',
            'mary',
            'tempo'
        );
        
        return in_array($character_name, $mode_characters, true);
    }
    
    public static function isOriginalCharacter($character_name) {
        $original_characters = array(
            "cadence",
            "bard",
            "aria",
            "bolt",
            "monk",
            "dove",
            "eli",
            "melody",
            "dorian",
            "coda",
            "story",
            "all"
        );
        
        return in_array($character_name, $original_characters, true);
    }
    
    public static function isAmplifiedDlcCharacter($character_name) {
        $dlc_characters = array(
            "cadence",
            "bard",
            "aria",
            "bolt",
            "monk",
            "dove",
            "eli",
            "melody",
            "dorian",
            "coda",
            "story",
            "all",
            'nocturna',
            'diamond',
            'mary',
            'tempo',
            'all_dlc'
        );
        
        return in_array($character_name, $dlc_characters, true);
    }
}
