<?php
namespace App\Components;

use Exception;
use App\Rules\NameExists;
use App\Releases;
use App\Modes;
use App\DailyRankingDayTypes;
use App\ExternalSites;
use App\Characters;
use App\LeaderboardTypes;
use App\LeaderboardSources;
use App\SeededTypes;
use App\Soundtracks;
use App\MultiplayerTypes;

class CommonApiValidationRules {
    protected static $plain_rules = [
        'date' => 'required|date_format:Y-m-d',
        'page' => 'sometimes|required|integer|min:1',
        'limit' => 'sometimes|required|integer|min:1|max:100',
        'search' => 'sometimes|required|string',
        'start_date' => 'required|date_format:Y-m-d|before_or_equal:end_date',
        'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
        'lbid' => 'required|integer',
        'player_id' => 'required|string',
        'leaderboard_id' => 'required|string'
    ];

    public static function getRules(array $rule_names) {    
        $rules = [];
    
        if(!empty($rule_names)) {
            foreach($rule_names as $rule_name) { 
                $rule = NULL;
            
                if(isset(static::$plain_rules[$rule_name])) {
                    $rule = static::$plain_rules[$rule_name];
                }
                else {
                    switch($rule_name) {
                        case 'release':
                            $rule = ['required', 'string', new NameExists(Releases::class)];
                            break;
                        case 'mode':
                            $rule = ['required', 'string', new NameExists(Modes::class)];
                            break;
                        case 'number_of_days':
                            $rule = ['required', 'string', new NameExists(DailyRankingDayTypes::class)];
                            break;
                        case 'site':
                            $rule = ['sometimes', 'required', 'string', new NameExists(ExternalSites::class)];
                            break;
                        case 'character':
                            $rule = ['required', 'string', new NameExists(Characters::class)];
                            break;
                        case 'leaderboard_source':
                            $rule = ['required', 'string', new NameExists(LeaderboardSources::class)];
                            break;
                        case 'leaderboard_type':
                            $rule = ['required', 'string', new NameExists(LeaderboardTypes::class)];
                            break;
                        case 'seeded_type':
                            $rule = ['required', 'string', new NameExists(SeededTypes::class)];
                            break;
                        case 'soundtrack':
                            $rule = ['required', 'string', new NameExists(Soundtracks::class)];
                            break;
                        case 'multiplayer_type':
                            $rule = ['required', 'string', new NameExists(MultiplayerTypes::class)];
                            break;
                        default:
                            throw new Exception("Specified validation rule '{$rule_name}' is not valid.");
                            break;
                        
                    }
                }
                
                $rules[$rule_name] = $rule;
            }
        }
        
        return $rules;
    }
}
