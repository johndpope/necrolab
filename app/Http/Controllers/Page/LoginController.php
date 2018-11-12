<?php

namespace App\Http\Controllers\Page;

use Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SteamUsers;
use App\Users;

class LoginController extends Controller {
    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('guest')->except('logout');
    }
    
    /**
     * Initializes the login process by redirection the user to Steam.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function loginSteam(Request $request) {    
        return Socialite::driver('steam')->redirect();
    }
    
    /**
     * Authenticates a steam user locally from a successful remote login.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function loginSteamSuccess(Request $request) {
        $user = Socialite::driver('steam')->user();
        
        // If a Steam authentication did not happen redirect this user to the homepage
        if(empty($user)) {
            return redirect('/');
        }
    
        $steam_user = SteamUsers::where('steamid', $user->user['steamid'])->first();
        
        if(empty($steam_user)) {
            /* ---------- Redirect back to the login page indicating that the user cannot be found ---------- */
            redirect('/#login?error=steam_not_exists');
        }
        
        /* ---------- Save this user's profile data ---------- */
        
        $steam_user->communityvisibilitystate = $user->user['communityvisibilitystate'];
        $steam_user->profilestate = $user->user['profilestate'];
        $steam_user->personaname = $user->user['personaname'];
        $steam_user->profileurl = $user->user['profileurl'];
        $steam_user->avatar = $user->user['avatar'];
        $steam_user->avatarmedium = $user->user['avatarmedium'];
        $steam_user->avatarfull = $user->user['avatarfull'];
        
        $steam_user->save();
        
        $user = Users::firstOrCreate([
            'steam_user_id' => $steam_user->steam_user_id
        ]);
        
        Auth::login($user, true);
        
        $request->session()->regenerate();
        
        return redirect()->intended('/');
    }
    
    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request) {
        Auth::logout();

        $request->session()->invalidate();

        return redirect('/');
    }
}
