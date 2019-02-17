<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Components\CommonApiValidationRules;

class ReadPowerRankingCharacterEntries extends Core {
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
            'leaderboard_source',
            'character',
            'release',
            'mode',
            'seeded_type',
            'multiplayer_type',
            'soundtrack',
            'date',
            'site',
            'search',
            'page',
            'limit'
        ]);
    }
}
