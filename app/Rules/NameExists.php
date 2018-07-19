<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class NameExists implements Rule {
    protected $model;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($model) {
        $this->model = $model;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value) {
        return !empty($this->model::getByName($value));
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message() {
        return "Specified property ':attribute' is not valid.";
    }
}
