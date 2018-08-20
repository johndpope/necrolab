<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use App\Http\Resources\LeaderboardSnapshotsResource;
use App\LeaderboardSnapshots;
use App\Releases;
use App\Modes;
use App\LeaderboardTypes;

class LeaderboardSnapshotsController extends Controller {
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api')->except([
            'index',
            'playerIndex'
        ]);
    }

    /**
     * Display a listing of all snapshots for this leaderboard.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index($lbid) {      
        return LeaderboardSnapshotsResource::collection(
            Cache::store('opcache')->remember("leaderboards:{$lbid}:snapshots", 5, function() use($lbid) {
                return LeaderboardSnapshots::getApiReadQuery($lbid)->get();
            })
        );
    }
    
    /**
     * Display a listing of all snapshots that a player has entries for in a specified leaderboard.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function playerIndex($steamid, $lbid) {
        $cache_key = "players:steam:{$steamid}:leaderboards:{$lbid}:snapshots";
        
        return LeaderboardSnapshotsResource::collection(
            Cache::store('opcache')->remember($cache_key, 5, function() use($steamid, $lbid) {
                return LeaderboardSnapshots::getPlayerApiDates($steamid, $lbid);
            })
        );
    }
}
