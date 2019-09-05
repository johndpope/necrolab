<?php

namespace App\Rules\Password;

use Exception;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Contracts\Validation\Rule;

class SpecialCharacters implements Rule
{
    /**
     * Initializes a new instance of this rule.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * Retrieves the error message for this rule if validation fails.
     *
     * @return array|Translator|string|null
     */
    public function message() {
        return "Password must contain at least 1 special character.";
    }

    /**
     * Indicates if the password contains at least 1 special character.
     *
     * @param string $attribute The name of the field to validate.
     * @param mixed $value The value of the field to validate.
     * @return bool
     * @throws Exception
     */
    public function passes($attribute, $value) {
        return !empty(preg_match("/[^[a-zA-Z0-9]]*?/", $value));
    }
}