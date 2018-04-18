<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Characters extends Model {
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