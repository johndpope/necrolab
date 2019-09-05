<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Users;

class EmailExists implements Rule {
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value) {
        $count = Users::withTrashed()->where('email_lowercase', strtolower($value))->count();

        return empty($count);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message() {
        return "Specified :attribute already exists.";
    }
}
