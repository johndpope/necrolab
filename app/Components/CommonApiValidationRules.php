<?php
namespace App\Components;

use Exception;
use App\Rules\UsernameExists;
use App\Rules\EmailExists;
use App\Rules\Password\Pwned as PasswordPwned;
use App\Rules\Password\LowerCaseCharacters as PasswordLowerCaseCharacters;
use App\Rules\Password\UpperCaseCharacters as PasswordUpperCaseCharacters;
use App\Rules\Password\Numbers as PasswordNumbers;
use App\Rules\Password\SpecialCharacters as PasswordSpecialCharacters;
use App\Rules\NameExists;
use App\Dates;
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
        'page' => 'sometimes|required|integer|min:1',
        'limit' => 'sometimes|required|integer|min:1|max:100',
        'search' => 'sometimes|required|string',
        'start_date' => 'required|date_format:Y-m-d|before_or_equal:end_date',
        'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
        'lbid' => 'required|integer',
        'player_id' => 'required|string',
        'leaderboard_id' => 'required|string'
    ];

    public static function getRules(array $rule_names): array {
        $rules = [];

        if(!empty($rule_names)) {
            foreach($rule_names as $rule_name) {
                $rule = NULL;

                if(isset(static::$plain_rules[$rule_name])) {
                    $rule = static::$plain_rules[$rule_name];
                }
                else {
                    switch($rule_name) {
                        case 'leaderboard_source':
                            $rule = ['required', 'string', new NameExists(LeaderboardSources::class)];
                            break;
                        case 'date':
                            $rule = ['required', 'string', 'date_format:Y-m-d', new NameExists(Dates::class)];
                            break;
                        case 'leaderboard_type':
                            $rule = ['required', 'string', new NameExists(LeaderboardTypes::class)];
                            break;
                        case 'character':
                            $rule = ['sometimes', 'required', 'string', new NameExists(Characters::class)];
                            break;
                        case 'release':
                            $rule = ['sometimes', 'required', 'string', new NameExists(Releases::class)];
                            break;
                        case 'mode':
                            $rule = ['sometimes', 'required', 'string', new NameExists(Modes::class)];
                            break;
                        case 'seeded_type':
                            $rule = ['sometimes', 'required', 'string', new NameExists(SeededTypes::class)];
                            break;
                        case 'multiplayer_type':
                            $rule = ['sometimes', 'required', 'string', new NameExists(MultiplayerTypes::class)];
                            break;
                        case 'soundtrack':
                            $rule = ['sometimes', 'required', 'string', new NameExists(Soundtracks::class)];
                            break;
                        case 'number_of_days':
                            $rule = ['required', 'string', new NameExists(DailyRankingDayTypes::class)];
                            break;
                        case 'site':
                            $rule = ['sometimes', 'required', 'string', new NameExists(ExternalSites::class)];
                            break;
                        case 'username':
                            $rule = [
                                'bail',
                                'required',
                                'string',
                                'between:4,25',
                                new UsernameExists()
                            ];
                            break;
                        case 'email':
                            $rule = [
                                'bail',
                                'required',
                                'email',
                                'max:255',
                                new EmailExists()
                            ];
                            break;
                        case 'password':
                            $rule = [
                                'bail',
                                'required',
                                'string',
                                'min:10',
                                'max:255',
                                new PasswordLowerCaseCharacters(),
                                new PasswordUpperCaseCharacters(),
                                new PasswordNumbers(),
                                new PasswordSpecialCharacters(),
                                new PasswordPwned(1),
                                'confirmed'
                            ];
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
