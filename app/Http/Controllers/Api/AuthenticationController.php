<?php

namespace App\Http\Controllers\Api;

use Illuminate\Foundation\Auth\ThrottlesLogins;
use App\Http\Requests\Api\UsernameExists;
use App\Http\Requests\Api\EmailExists;
use App\Http\Requests\Api\PasswordValid;
use App\Http\Resources\UsernameExistsResource;
use App\Http\Resources\EmailExistsResource;
use App\Http\Resources\PasswordValidResource;
use App\Http\Controllers\Controller;

class AuthenticationController extends Controller {
    use ThrottlesLogins;

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('throttle:20,1');
    }

    /**
     * Checks if the specified username exists in the database.
     *
     * @param \App\Http\Requests\Api\UsernameExists $request
     * @return \App\Http\Resources\UsernameExistsResource
     */
    public function usernameExists(UsernameExists $request) {
        return new UsernameExistsResource(true);
    }

    /**
     * Checks if the specified email exists in the database.
     *
     * @param \App\Http\Requests\Api\UsernameExists $request
     * @return \App\Http\Resources\EmailExistsResource
     */
    public function emailExists(EmailExists $request) {
        return new EmailExistsResource(true);
    }

    /**
     * Checks if the specified email exists in the database.
     *
     * @param \App\Http\Requests\Api\UsernameExists $request
     * @return \App\Http\Resources\PasswordValidResource
     */
    public function passwordValid(PasswordValid $request) {
        return new PasswordValidResource(true);
    }
}
