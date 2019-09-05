<?php

namespace App\Rules\Password;

use Exception;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Contracts\Validation\Rule;

class UpperCaseCharacters implements Rule
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
        return "Password must contain at least 1 upper case letter.";
    }

    /**
     * Indicates if the password contains at least 1 upper case character.
     *
     * @param string $attribute The name of the field to validate.
     * @param mixed $value The value of the field to validate.
     * @return bool
     * @throws Exception
     */
    public function passes($attribute, $value) {
        return !empty(preg_match("/[A-Z]/", $value));
    }
}