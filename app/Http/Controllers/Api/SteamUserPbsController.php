<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Components\Dataset\Dataset;
use App\Components\Dataset\DataProviders\Sql as SqlDataProvider;
use App\Http\Controllers\Controller;
use App\Http\Resources\SteamUserPbsResource;
use App\Http\Requests\Api\ReadSteamUserPbs;
use App\SteamUserPbs;
use App\Characters;
use App\Releases;
use App\Modes;
use App\LeaderboardTypes;

class SteamUserPbsController extends Controller {
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api')->except([
            'playerScoreIndex',
            'playerSpeedIndex',
            'playerDeathlessIndex'
        ]);
    }
    
    /**
     * Shared method for player pb endpoints in this controller.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param \App\LeaderboardTypes $leaderboard_type
     * @return \Illuminate\Http\Response
     */
    protected function getPlayerResponse(Request $request, LeaderboardTypes $leaderboard_type) {
        $validated_request = $request->validated();
        
        $steamid = $request->steamid;
        $character_id = Characters::getByName($validated_request['character'])->character_id;
        $release_id = Releases::getByName($validated_request['release'])->release_id;
        $mode_id = Modes::getByName($validated_request['mode'])->mode_id;
        $leaderboard_type_id = $leaderboard_type->leaderboard_type_id;
        
        $seeded = $validated_request['seeded'];
        $co_op = $validated_request['co_op'];
        $custom = $validated_request['custom'];
        
        $cache_key = "players:steam:{$steamid}:pbs:{$character_id}:{$release_id}:{$mode_id}:{$leaderboard_type_id}:{$seeded}:{$co_op}:{$custom}";
        
        return SteamUserPbsResource::collection(
            Cache::store('opcache')->remember($cache_key, 5, function() use(
                $steamid,
                $character_id, 
                $release_id, 
                $mode_id, 
                $leaderboard_type_id, 
                $seeded,
                $co_op,
                $custom
            ) {            
                return SteamUserPbs::getPlayerApiReadQuery(
                    $steamid,
                    $character_id,
                    $release_id,
                    $mode_id,
                    $leaderboard_type_id,
                    $seeded,
                    $co_op,
                    $custom
                )->get();
            })
        );
    }

    /**
     * Display a listing of a player's score pbs.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function playerScoreIndex(ReadSteamUserPbs $request) {
        return $this->getPlayerResponse(
            $request,
            LeaderboardTypes::getByName('score')
        );
    }
    
    /**
     * Display a listing of a player's speed pbs.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function playerSpeedIndex(ReadSteamUserPbs $request) {
        return $this->getPlayerResponse(
            $request,
            LeaderboardTypes::getByName('speed')
        );
    }
    
    /**
     * Display a listing of a player's deathless pbs.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function playerDeathlessIndex(ReadSteamUserPbs $request) {
        return $this->getPlayerResponse(
            $request,
            LeaderboardTypes::getByName('deathless')
        );
    }
}
