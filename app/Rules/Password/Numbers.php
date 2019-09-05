<?php

namespace App\Rules\Password;

use Exception;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Contracts\Validation\Rule;

class Numbers implements Rule
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
        return "Password must contain at least 1 number.";
    }

    /**
     * Indicates if the password contains at least 1 number.
     *
     * @param string $attribute The name of the field to validate.
     * @param mixed $value The value of the field to validate.
     * @return bool
     * @throws Exception
     */
    public function passes($attribute, $value) {
        return !empty(preg_match("/[0-9]/", $value));
    }
}