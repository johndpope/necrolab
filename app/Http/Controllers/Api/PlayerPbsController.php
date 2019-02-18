<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use App\Components\RequestModels;
use App\Components\Dataset\Dataset;
use App\Components\Dataset\DataProviders\Sql as SqlDataProvider;
use App\Http\Resources\PlayerPbsResource;
use App\Http\Requests\Api\ReadPlayerPbs;
use App\PlayerPbs;
use App\LeaderboardSources;
use App\LeaderboardTypes;
use App\Characters;
use App\Releases;
use App\Modes;
use App\SeededTypes;
use App\MultiplayerTypes;
use App\Soundtracks;

class PlayerPbsController extends Controller {
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api')->except([
            'playerIndex'
        ]);
    }
    
    /**
     * Shared method for player pb endpoints in this controller.
     *
     * @param  \App\Http\Requests\Api\ReadPlayerPbs  $request
     * @return \Illuminate\Http\Response
     */
    public function playerIndex(ReadPlayerPbs $request) {
        $request_models = new RequestModels($request, [
            'leaderboard_source',
            'leaderboard_type',
            'character',
            'release',
            'mode',
            'seeded_type',
            'multiplayer_type',
            'soundtrack'
        ]);
        
        $player_id = $request->player_id;
        
        $cache_prefix_name = $request_models->getCacheNamePrefix();
        
        $cache_key = "players:{$player_id}:pbs:" . (string)$cache_prefix_name;
        
        return PlayerPbsResource::collection(
            Cache::store('opcache')->remember(
                $cache_key, 
                5, 
                function() use($player_id, $request_models) {            
                    return PlayerPbs::getPlayerApiReadQuery(
                        $player_id,
                        $request_models->leaderboard_source,
                        $request_models->leaderboard_type,
                        $request_models->character,
                        $request_models->release,
                        $request_models->mode,
                        $request_models->seeded_type,
                        $request_models->multiplayer_type,
                        $request_models->soundtrack
                    )->get();
                })
        );
    }
}
