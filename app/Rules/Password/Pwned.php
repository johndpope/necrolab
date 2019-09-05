<?php

namespace App\Rules\Password;

use Exception;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Contracts\Validation\Rule;
use App\Components\HaveIBeenPwnedApi\SearchPasswordByRange;

class Pwned implements Rule
{
    /**
     * @var int The number of matches to start flagging a password as pwned.
     */
    protected $matches_threshold;

    /**
     * Initializes a new instance of this rule.
     *
     * @param int $matches_threshold (optional) The number of matches to start flagging a password as pwned.
     * @return void
     */
    public function __construct(int $matches_threshold = 10) {
        $this->matches_threshold = $matches_threshold;
    }

    /**
     * Retrieves the error message for this rule if validation fails.
     *
     * @return array|Translator|string|null
     */
    public function message() {
        return "This password is too easy to guess. Please use a more complex password.";
    }

    /**
     * Indicates if the password goes not match anything on HaveIBeenPwned.
     *
     * @param string $attribute The name of the field to validate.
     * @param mixed $value The value of the field to validate.
     * @return bool
     * @throws Exception
     */
    public function passes($attribute, $value) {
        $haveibeenpwned_api = new SearchPasswordByRange($value);

        $haveibeenpwned_api->search();

        return empty($haveibeenpwned_api->getMatches($this->matches_threshold));
    }
}