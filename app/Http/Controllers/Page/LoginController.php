<?php

namespace App\Http\Controllers\Page;

use Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Players;
use App\Users;
use App\UserSteamPlayer;

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
    
        $player = Players::setSchemaStatic('steam')->where('external_id', $user->user['steamid'])->first();
        
        if(empty($player)) {
            /* ---------- Redirect back to the login page indicating that the user cannot be found ---------- */
            redirect('/#login?error=steam_not_exists');
        }
        
        /* ---------- Save this user's profile data ---------- */
        
        $player->setSchema('steam');
        
        $player->username = $user->user['personaname'];
        $player->profile_url = $user->user['profileurl'];
        $player->avatar_url  = $user->user['avatar'];
        $player->updated = date('Y-m-d H:i:s');
        
        $player->save();
        
        
        $user_steam_player = UserSteamPlayer::where('player_id', $player->id)->first();
        
        $user_record = NULL;
        
        if(empty($user_steam_player)) {
            // Create the user record
            $user_record = new Users();
            
            $user_record->created_at = date('Y-m-d H:i:s');
            
            $user_record->save();
            
            // Create the record that links a user to a player
            $user_steam_player = new UserSteamPlayer();
            
            $user_steam_player->user_id = $user_record->id;
            $user_steam_player->player_id = $player->id;
            
            $user_steam_player->save();
        }
        else {
            $user_record = Users::where('id', $user_steam_player->user_id)->first();
        }
        
        Auth::login($user_record, true);
        
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
