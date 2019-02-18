<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use App\Components\RequestModels;
use App\Components\Encoder;
use App\Http\Requests\Api\ReadLeaderboardSnapshots;
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
    public function index(ReadLeaderboardSnapshots $request) {
        $request_models = new RequestModels($request, [
            'leaderboard_source',
        ]);
        
        $leaderboard_id = $request->leaderboard_id;
        
        $cache_prefix_name = $request_models->getCacheNamePrefix();
        
        $cache_prefix_name->leaderboard_id = $leaderboard_id;
        
        $cache_key = "leaderboard:snapshots:" . (string)$cache_prefix_name;
    
        return LeaderboardSnapshotsResource::collection(
            Cache::store('opcache')->remember($cache_key, 5, function() use($request_models, $leaderboard_id) {
                $records = LeaderboardSnapshots::getApiReadQuery($request_models->leaderboard_source, $leaderboard_id)->get();
                
                Encoder::jsonDecodeProperties($records, [
                    'details'
                ]);
                
                return $records;
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
        $request_models = new RequestModels($request, [
            'leaderboard_source',
        ]);
        
        $player_id = $request->player_id;
        $leaderboard_id = $request->leaderboard_id;
        
        $cache_prefix_name = $request_models->getCacheNamePrefix();
        
        $cache_prefix_name->player_id = $player_id;
        $cache_prefix_name->leaderboard_id = $leaderboard_id;
    
        $cache_key = "player:leaderboard:snapshots:" . (string)$cache_prefix_name;
        
        return PlayerLeaderboardSnapshotsResource::collection(
            Cache::store('opcache')->remember($cache_key, 5, function() use($request_models, $player_id, $leaderboard_id) {
                return LeaderboardSnapshots::getPlayerApiDates($player_id, $request_models->leaderboard_source, $leaderboard_id);
            })
        );
    }
}
