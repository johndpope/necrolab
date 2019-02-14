<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Components\CommonApiValidationRules;

class ReadPlayerDailyRankingEntries extends Core {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return CommonApiValidationRules::getRules([
            'player_id',
            'leaderboard_source',
            'character',
            'release',
            'mode',
            'multiplayer_type',
            'soundtrack',
            'number_of_days',
            'page',
            'limit'
        ]);
    }
}
