<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ReadPlayerLeaderboardSnapshots;
use App\Http\Resources\LeaderboardSnapshotsResource;
use App\Http\Resources\PlayerLeaderboardSnapshotsResource;
use App\LeaderboardSources;
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
    public function playerIndex(ReadPlayerLeaderboardSnapshots $request) {
        $leaderboard_source = LeaderboardSources::getByName($request->leaderboard_source);
        
        $player_id = $request->player_id;
        $leaderboard_id = $request->leaderboard_id;
    
        $cache_key = "players:{$leaderboard_source->name}:{$player_id}:leaderboards:{$leaderboard_id}:snapshots";
        
        return PlayerLeaderboardSnapshotsResource::collection(
            Cache::store('opcache')->remember($cache_key, 5, function() use($player_id, $leaderboard_source, $leaderboard_id) {
                return LeaderboardSnapshots::getPlayerApiDates($player_id, $leaderboard_source, $leaderboard_id);
            })
        );
    }
}
