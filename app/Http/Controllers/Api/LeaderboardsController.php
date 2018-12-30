<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use App\Http\Resources\LeaderboardsResource;
use App\Http\Resources\DailyLeaderboardsResource;
use App\Http\Resources\LeaderboardsXmlResource;
use App\Http\Requests\Api\ReadLeaderboards;
use App\Http\Requests\Api\ReadLeaderboardByAttributes;
use App\Http\Requests\Api\ReadCategoryLeaderboards;
use App\Http\Requests\Api\ReadDailyLeaderboards;
use App\Leaderboards;
use App\LeaderboardSources;
use App\LeaderboardTypes;
use App\Releases;
use App\Modes;
use App\Characters;
use App\SeededTypes;
use App\MultiplayerTypes;
use App\Soundtracks;

class LeaderboardsController extends Controller {
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api')->except([
            'index',
            'categoryIndex',
            'dailyIndex',
            'byAttributes',
            'show',
            'xmlIndex',
            'playerIndex',
            'playerCategoryIndex',
            'playerDailyIndex'
        ]);
    }

    /**
     * Display a listing of all leaderboards.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(ReadLeaderboards $request) {
        $release_id = Releases::getByName($request->release)->release_id;
        $mode_id = Modes::getByName($request->mode)->mode_id;
        $character_id = Characters::getByName($request->character)->character_id;
        
        return LeaderboardsResource::collection(
            Cache::store('opcache')->remember(
                "leaderboards:steam:{$release_id}:{$mode_id}:{$character_id}", 
                5, 
                function() use($release_id, $mode_id, $character_id) {
                    return Leaderboards::getNonDailyApiReadQuery(
                        $release_id,
                        $mode_id,
                        $character_id
                    )->get();
                }
            )
        );
    }
    
    /**
     * Display a listing of all score leaderboards.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function categoryIndex(ReadCategoryLeaderboards $request) {
        $leaderboard_type_id = LeaderboardTypes::getByName($request->leaderboard_type)->leaderboard_type_id;
        $release_id = Releases::getByName($request->release)->release_id;
        $mode_id = Modes::getByName($request->mode)->mode_id;
        $character_id = Characters::getByName($request->character)->character_id;
        
        return LeaderboardsResource::collection(
            Cache::store('opcache')->remember(
                "leaderboards:steam:category:{$leaderboard_type_id}:{$release_id}:{$mode_id}:{$character_id}", 
                5, 
                function() use($leaderboard_type_id, $release_id, $mode_id, $character_id) {
                    return Leaderboards::getCategoryApiReadQuery(
                        $leaderboard_type_id,
                        $release_id,
                        $mode_id,
                        $character_id
                    )->get();
                }
            )
        );
    }
    
    /**
     * Display a leaderboard based on its attributes.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function byAttributes(ReadLeaderboardByAttributes $request) {
        $leaderboard_source_id = LeaderboardSources::getByName($request->leaderboard_source)->id;
        $leaderboard_type_id = LeaderboardTypes::getByName($request->leaderboard_type)->leaderboard_type_id;
        $character_id = Characters::getByName($request->character)->character_id;
        $release_id = Releases::getByName($request->release)->release_id;
        $mode_id = Modes::getByName($request->mode)->mode_id;
        $seeded_type_id = SeededTypes::getByName($request->seeded_type)->id;
        $multiplayer_type_id = MultiplayerTypes::getByName($request->multiplayer_type)->id;
        $soundtrack_id = Soundtracks::getByName($request->soundtrack)->id;
        
        $cache_key = "leaderboards:{$leaderboard_source_id}:{$leaderboard_type_id}:{$character_id}:{$release_id}:{$mode_id}:{$seeded_type_id}:{$multiplayer_type_id}:{$soundtrack_id}";
    
        return new LeaderboardsResource(
            Cache::store('opcache')->remember(
                $cache_key, 
                5, 
                function() use(
                    $leaderboard_source_id,
                    $leaderboard_type_id,
                    $character_id,
                    $release_id,
                    $mode_id,
                    $seeded_type_id,
                    $multiplayer_type_id,
                    $soundtrack_id
                ) {
                    return Leaderboards::getApiByAttributesQuery(
                        $leaderboard_source_id,
                        $leaderboard_type_id,
                        $character_id,
                        $release_id,
                        $mode_id,
                        $seeded_type_id,
                        $multiplayer_type_id,
                        $soundtrack_id
                    )->first();
                }
            )
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $lbid
     * @return \Illuminate\Http\Response
     */
    public function show($lbid) {
        return new LeaderboardsResource(
            Leaderboards::getApiShowQuery($lbid)->first()
        );
    }
    
    /**
     * Display a listing of all daily leaderboards.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function dailyIndex(ReadDailyLeaderboards $request) {
        $leaderboard_source_id = LeaderboardSources::getByName($request->leaderboard_source)->id;
        $character_id = Characters::getByName($request->character)->character_id;
        $release_id = Releases::getByName($request->release)->release_id;
        $mode_id = Modes::getByName($request->mode)->mode_id;
        $multiplayer_type_id = MultiplayerTypes::getByName($request->multiplayer_type)->id;
        
        $cache_key = "leaderboards:steam:daily:{$leaderboard_source_id}:{$character_id}:{$release_id}:{$mode_id}:{$multiplayer_type_id}";
        
        return DailyLeaderboardsResource::collection(
            Cache::store('opcache')->remember($cache_key, 5, function() use(
                $leaderboard_source_id,
                $character_id,
                $release_id, 
                $mode_id,
                $multiplayer_type_id
            ) {
                return Leaderboards::getDailyApiReadQuery(
                    $leaderboard_source_id,
                    $character_id,
                    $release_id, 
                    $mode_id,
                    $multiplayer_type_id
                )->get();
            })
        );
    }
    
    /**
     * Display a listing of all leaderboard xml entries.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function xmlIndex() {        
        return LeaderboardsXmlResource::collection(
            Cache::store('opcache')->remember("leaderboards:xml", 5, function() {
                return collect(Leaderboards::getXmlUrls());
            })
        );
    }
    
    /**
     * Display a listing of the leaderboards a player has an entry in.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function playerIndex(ReadLeaderboards $request) {
        $leaderboard_source = LeaderboardSources::getByName($request->leaderboard_source);
    
        $player_id = $request->player_id;
        $release_id = Releases::getByName($request->release)->release_id;
        $mode_id = Modes::getByName($request->mode)->mode_id;
        $character_id = Characters::getByName($request->character)->character_id;
        
        $cache_key = "player:{$leaderboard_source->name}:{$player_id}:leaderboards:{$release_id}:{$mode_id}:{$character_id}";
        
        return LeaderboardsResource::collection(
            Cache::store('opcache')->remember($cache_key, 5, function() use($player_id, $leaderboard_source, $release_id, $mode_id, $character_id) {
                return Leaderboards::getPlayerNonDailyApiReadQuery(
                    $player_id,
                    $leaderboard_source,
                    $release_id,
                    $mode_id,
                    $character_id
                )->get();
            })
        );
    }
    
    /**
     * Display a listing of the score leaderboards a player has an entry in.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function playerCategoryIndex(ReadCategoryLeaderboards $request) {
        $leaderboard_source = LeaderboardSources::getByName($request->leaderboard_source);
    
        $player_id = $request->player_id;
        $leaderboard_type_id = LeaderboardTypes::getByName($request->leaderboard_type_id)->leaderboard_type_id;
        $release_id = Releases::getByName($request->release)->release_id;
        $mode_id = Modes::getByName($request->mode)->mode_id;
        $character_id = Characters::getByName($request->character)->character_id;
        
        $cache_key = "player:{$leaderboard_source->name}:{$player_id}:leaderboards:category:{$leaderboard_type_id}:{$release_id}:{$mode_id}:{$character_id}";
        
        return LeaderboardsResource::collection(
            Cache::store('opcache')->remember($cache_key, 5, function() use(
                $player_id, 
                $leaderboard_source, 
                $leaderboard_type_id, 
                $release_id, 
                $mode_id, 
                $character_id
            ) {
                return Leaderboards::getPlayerCategoryApiReadQuery(
                    $player_id,
                    $leaderboard_source,
                    $leaderboard_type_id,
                    $release_id,
                    $mode_id,
                    $character_id
                )->get();
            })
        );
    }
    
    /**
     * Display a listing of all daily leaderboards that a player has an entry in.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function playerDailyIndex(ReadDailyLeaderboards $request) {
        $leaderboard_source = LeaderboardSources::getByName($request->leaderboard_source);
    
        $player_id = $request->player_id;
        $release_id = Releases::getByName($request->release)->release_id;
        $mode_id = Modes::getByName($request->mode)->mode_id;
        
        $cache_key = "player:{$leaderboard_source->name}:{$player_id}:leaderboards:daily:{$release_id}:{$mode_id}";
        
        return DailyLeaderboardsResource::collection(
            Cache::store('opcache')->remember($cache_key, 5, function() use($player_id, $leaderboard_source, $release_id, $mode_id) {
                return Leaderboards::getPlayerDailyApiReadQuery(
                    $player_id,
                    $leaderboard_source,
                    $release_id,
                    $mode_id
                )->get();
            })
        );
    }
}
