<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Components\CommonApiValidationRules;

class ReadSteamUserDailyLeaderboardEntries extends Core {
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
            'release',
            'page',
            'limit'
        ]);
    }
}
