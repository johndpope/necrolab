<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Components\RegexCombiner;
use App\LeaderboardSources;

/*
 * This trait depends on the GetById and HasDefaultRecord traits defined in the model it is attached to.
 */
trait MatchesOnString {       
    protected static $match_critera_records = [];
    
    protected static $combined_match_regex = [];
    
    abstract protected static function getMatchModel(): string;
    
    abstract protected static function getMatchFieldIdName(): string;
    
    protected static function loadMatchRecords(LeaderboardSources $leaderboard_source): void {
        if(!isset(static::$match_critera_records[$leaderboard_source->name])) {
            $match_model_class = static::getMatchModel();
            $match_field_id_name = static::getMatchFieldIdName();
        
            static::$match_critera_records[$leaderboard_source->name] = $match_model_class::where('leaderboard_source_id', $leaderboard_source->id)
                ->orderBy('sort_order', 'asc')
                ->get()
                ->keyBy($match_field_id_name);
        }
    }
    
    protected static function loadRegexCombiner(LeaderboardSources $leaderboard_source): void {
        if(!isset(static::$combined_match_regex[$leaderboard_source->name])) {
            static::$combined_match_regex[$leaderboard_source->name] = '';
        
            if(!empty(isset(static::$match_critera_records[$leaderboard_source->name]))) {
                $match_criteria_records = static::$match_critera_records[$leaderboard_source->name];
                
                $regex_combiner = new RegexCombiner();
                
                foreach($match_criteria_records as $match_field_id => $match_criteria_record) {
                    $regex_combiner->addSegment($match_field_id, $match_criteria_record->match_regex);
                }
                
                static::$combined_match_regex[$leaderboard_source->name] = $regex_combiner->getCombined();
            }
        }
    }
    
    public static function getMatchFromString(LeaderboardSources $leaderboard_source, string $unmatched): Model {
        static::loadMatchRecords($leaderboard_source);
        
        static::loadRegexCombiner($leaderboard_source);        
        
        $match_regex = static::$combined_match_regex[$leaderboard_source->name];
        
        $matches = [];
        
        // Run the combined regex against the unmatched string
        preg_match($match_regex, $unmatched, $matches);
        
        // If a match is found, and the record exists, then retrieve its record
        if(!empty($matches['MARK']) && !empty(static::$match_critera_records[$leaderboard_source->name][$matches['MARK']])) {
            $matched_record = static::getById($matches['MARK']);
        }
        
        // If no matches are found then load the default record
        if(empty($matched_record)) {
            $matched_record = static::getDefaultRecord($leaderboard_source);
        }

        // If no matches are still found then throw an exception indicating such.
        if(empty($matched_record)) {
            throw new Exception("There are no matching records for '{$string}' and no default record has been specified.");
        }
        
        return $matched_record;
    }
}
