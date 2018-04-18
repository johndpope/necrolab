<?php

namespace App\Models\Leaderboards;

use Illuminate\Database\Eloquent\Model;

class Leaderboards extends Model {
    use NameGeneration, CsvData, XmlData;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'leaderboards';
    
    /**
     * The primary key associated with the model.
     *
     * @var string
     */
    protected $primaryKey = 'leaderboard_id';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    
    public static function getSeededFlagFromName($seeded_name) {
        $is_seeded = NULL;
        
        switch($seeded_name) {
            case 'seeded':
                $is_seeded = 1;
                break;
            case 'unseeded':
                $is_seeded = 0;
                break;
        }
        
        return $is_seeded;
    }
    
    public static function getCoOpFlagFromName($co_op_name) {
        $co_op = NULL;
        
        switch($co_op_name) {
            case 'co_op':
                $co_op = 1;
                break;
            case 'single':
                $co_op = 0;
                break;
        }
        
        return $co_op;
    }
    
    public static function getCustomFlagFromName($custom_name) {
        $custom = NULL;
        
        switch($custom_name) {
            case 'custom':
                $custom = 1;
                break;
            case 'default':
                $custom = 0;
                break;
        }
        
        return $custom;
    }
}