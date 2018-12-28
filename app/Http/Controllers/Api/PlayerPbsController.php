<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Components\Dataset\Dataset;
use App\Components\Dataset\DataProviders\Sql as SqlDataProvider;
use App\Http\Controllers\Controller;
use App\Http\Resources\PlayerPbsResource;
use App\Http\Requests\Api\ReadPlayerPbs;
use App\PlayerPbs;
use App\Characters;
use App\Releases;
use App\Modes;
use App\LeaderboardTypes;
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
        $leaderboard_type_id = LeaderboardTypes::getByName($request->leaderboard_type)->leaderboard_type_id;
        $steamid = $request->steamid;
        $character_id = Characters::getByName($request->character)->character_id;
        $release_id = Releases::getByName($request->release)->release_id;
        $mode_id = Modes::getByName($request->mode)->mode_id;
        
        $seeded_type_id = $mode_id = SeededTypes::getByName($request->seeded_type)->id;
        $multiplayer_type_id = MultiplayerTypes::getByName($request->multiplayer_type)->id;
        $soundtrack_id = Soundtracks::getByName($request->soundtrack)->id;
        
        $cache_key = "players:steam:{$steamid}:pbs:{$character_id}:{$release_id}:{$mode_id}:{$leaderboard_type_id}:{$seeded_type_id}:{$multiplayer_type_id}:{$soundtrack_id}";
        
        return PlayerPbsResource::collection(
            Cache::store('opcache')->remember($cache_key, 5, function() use(
                $steamid,
                $character_id, 
                $release_id, 
                $mode_id, 
                $leaderboard_type_id, 
                $seeded_type_id,
                $multiplayer_type_id,
                $soundtrack_id
            ) {            
                return PlayerPbs::getPlayerApiReadQuery(
                    $steamid,
                    $character_id,
                    $release_id,
                    $mode_id,
                    $leaderboard_type_id,
                    $seeded_type_id,
                    $multiplayer_type_id,
                    $soundtrack_id
                )->get();
            })
        );
    }
}
