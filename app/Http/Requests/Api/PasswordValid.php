<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Components\CommonApiValidationRules;
use App\Http\Requests\Api\Core;

class PasswordValid extends Core {
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
        $rules = CommonApiValidationRules::getRules([
            'password'
        ]);

        // Since we're validating the password we don't need confirmation
        array_pop($rules['password']);

        return $rules;
    }
}
