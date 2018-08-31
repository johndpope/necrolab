<?php

namespace App;

use Illuminate\Support\Facades\Auth;
use ElcoBvg\Opcache\Model;
use App\Traits\GetById;
use App\Traits\GetByName;
use App\Traits\StoredInCache;

class Characters extends Model {
    use GetById, GetByName, StoredInCache;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'characters';
    
    /**
     * The primary key associated with the model.
     *
     * @var string
     */
    protected $primaryKey = 'character_id';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    
    public static function getValidationRules() {
        return [
            'name' => 'required|max:100|unique:characters',
            'display_name' => 'required|max:255',
            'is_active' => 'required|integer|min:0|max:1',
            'sort_order' => 'required|integer|min:1',
        ];
    }
    
    public static function getStoredInCacheQuery() {
        return static::where('is_active', 1)
            ->orderBy('sort_order', 'asc');
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
    
    public static function getRecordFromMatch($string, array $characters) {
        $matched_character = NULL;
        $default_character = NULL;
    
        if(!empty($characters)) {
            foreach($characters as $character) {
                if($character->name == 'cadence') {
                    $default_character = $character;
                }
            
                if(stripos($string, $character->steam_match) !== false) {
                    $matched_character = $character;
                }
            }
        }
        
        if(empty($matched_character)) {
            $matched_character = $default_character;
        }
        
        return $matched_character;
    }
    
    public static function getAllActive() {
        $characters = static::all();
        
        $active_characters = [];
        
        foreach($characters as $character) {
            if(!empty($character->is_active)) {
                $active_characters[$character->name] = $character;
            }
        }
        
        return $active_characters;
    }
}
