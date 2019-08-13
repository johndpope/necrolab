<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use App\Http\Resources\LeaderboardsResource;
use App\Http\Resources\DailyLeaderboardsResource;
use App\Http\Resources\LeaderboardsXmlResource;
use App\Http\Requests\Api\ReadLeaderboards;
use App\Http\Requests\Api\ReadLeaderboardShow;
use App\Http\Requests\Api\ReadLeaderboardByAttributes;
use App\Http\Requests\Api\ReadCategoryLeaderboards;
use App\Http\Requests\Api\ReadDailyLeaderboards;
use App\Http\Requests\Api\ReadPlayerLeaderboards;
use App\Http\Requests\Api\ReadPlayerCategoryLeaderboards;
use App\Http\Requests\Api\ReadCharacterLeaderboards;
use App\Http\Requests\Api\ReadPlayerDailyLeaderboards;
use App\Components\RequestModels;
use App\Components\Encoder;
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
            'charactersIndex',
            'dailyIndex',
            'byAttributes',
            'show',
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
        $request_models = new RequestModels($request, [
            'leaderboard_source',
            'character',
            'release',
            'mode'
        ]);

        $cache_prefix_name = $request_models->getCacheNamePrefix();

        return LeaderboardsResource::collection(
            Cache::store('opcache')->remember(
                "leaderboards:" . (string)$cache_prefix_name,
                300,
                function() use($request_models) {
                    return Leaderboards::getNonDailyApiReadQuery(
                        $request_models->leaderboard_source,
                        $request_models->character,
                        $request_models->release,
                        $request_models->mode
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
        $request_models = new RequestModels($request, [
            'leaderboard_source',
            'leaderboard_type',
            'character',
            'release',
            'mode'
        ]);

        $cache_prefix_name = $request_models->getCacheNamePrefix();

        return LeaderboardsResource::collection(
            Cache::store('opcache')->remember(
                "leaderboards:category:" . (string)$cache_prefix_name,
                300,
                function() use($request_models) {
                    return Leaderboards::getCategoryApiReadQuery(
                        $request_models->leaderboard_source,
                        $request_models->leaderboard_type,
                        $request_models->character,
                        $request_models->release,
                        $request_models->mode
                    )->get();
                }
            )
        );
    }

    /**
     * Display a listing of all character leaderboards for the specified criteria.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function charactersIndex(ReadCharacterLeaderboards $request) {
        $request_models = new RequestModels($request, [
            'leaderboard_source',
            'leaderboard_type',
            'release',
            'mode',
            'seeded_type',
            'multiplayer_type',
            'soundtrack'
        ]);

        $cache_prefix_name = $request_models->getCacheNamePrefix();

        return LeaderboardsResource::collection(
            Cache::store('opcache')->remember(
                "leaderboards:characters:" . (string)$cache_prefix_name,
                300,
                function() use($request_models) {
                    return Leaderboards::getCharactersApiReadQuery(
                        $request_models->leaderboard_source,
                        $request_models->leaderboard_type,
                        $request_models->release,
                        $request_models->mode,
                        $request_models->seeded_type,
                        $request_models->multiplayer_type,
                        $request_models->soundtrack
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

        $cache_prefix_name = $request_models->getCacheNamePrefix();

        $cache_key = "leaderboards:by_attributes:" . (string)$cache_prefix_name;

        return new LeaderboardsResource(
            Cache::store('opcache')->remember(
                $cache_key,
                300,
                function() use($request_models) {
                    return Leaderboards::getApiByAttributesQuery(
                        $request_models->leaderboard_source,
                        $request_models->leaderboard_type,
                        $request_models->character,
                        $request_models->release,
                        $request_models->mode,
                        $request_models->seeded_type,
                        $request_models->multiplayer_type,
                        $request_models->soundtrack
                    )->first();
                }
            )
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(ReadLeaderboardShow $request) {
        $request_models = new RequestModels($request, [
            'leaderboard_source'
        ]);

        $leaderboard_id = $request->leaderboard_id;

        $cache_prefix_name = $request_models->getCacheNamePrefix();

        return new LeaderboardsResource(
            Cache::store('opcache')->remember(
                "leaderboards:show:" . (string)$cache_prefix_name . ":{$leaderboard_id}",
                300,
                function() use($request_models, $leaderboard_id) {
                    return Leaderboards::getApiShowQuery($request_models->leaderboard_source, $leaderboard_id)->first();
                }
            )
        );
    }

    /**
     * Display a listing of all daily leaderboards.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function dailyIndex(ReadDailyLeaderboards $request) {
        $request_models = new RequestModels($request, [
            'leaderboard_source',
            'character',
            'release',
            'mode',
            'multiplayer_type',
            'soundtrack'
        ]);

        $cache_prefix_name = $request_models->getCacheNamePrefix();

        $cache_key = "leaderboards:daily:" . (string)$cache_prefix_name;

        return DailyLeaderboardsResource::collection(
            Cache::store('opcache')->remember($cache_key, 300, function() use($request_models) {
                $records = Leaderboards::getDailyApiReadQuery(
                    $request_models->leaderboard_source,
                    $request_models->character,
                    $request_models->release,
                    $request_models->mode,
                    $request_models->multiplayer_type,
                    $request_models->soundtrack
                )->get();

                Encoder::jsonDecodeProperties($records, [
                    'details'
                ]);

                return $records;
            })
        );
    }

    /**
     * Display a listing of the leaderboards a player has an entry in.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function playerIndex(ReadPlayerLeaderboards $request) {
        $request_models = new RequestModels($request, [
            'leaderboard_source',
            'character',
            'release',
            'mode'
        ]);

        $player_id = $request->player_id;

        $cache_prefix_name = $request_models->getCacheNamePrefix();

        $cache_key = "player:{$player_id}:leaderboards:" . (string)$cache_prefix_name;

        return LeaderboardsResource::collection(
            Cache::store('opcache')->remember($cache_key, 300, function() use($player_id, $request_models) {
                return Leaderboards::getPlayerNonDailyApiReadQuery(
                    $request_models->leaderboard_source,
                    $player_id,
                    $request_models->character,
                    $request_models->release,
                    $request_models->mode
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
    public function playerCategoryIndex(ReadPlayerCategoryLeaderboards $request) {
        $request_models = new RequestModels($request, [
            'leaderboard_source',
            'leaderboard_type',
            'character',
            'release',
            'mode'
        ]);

        $player_id = $request->player_id;

        $cache_prefix_name = $request_models->getCacheNamePrefix();

        $cache_key = "player:{$player_id}:leaderboards:category:" . (string)$cache_prefix_name;

        return LeaderboardsResource::collection(
            Cache::store('opcache')->remember($cache_key, 300, function() use(
                $player_id,
                $request_models
            ) {
                return Leaderboards::getPlayerCategoryApiReadQuery(
                    $request_models->leaderboard_source,
                    $player_id,
                    $request_models->leaderboard_type,
                    $request_models->character,
                    $request_models->release,
                    $request_models->mode
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
    public function playerDailyIndex(ReadPlayerDailyLeaderboards $request) {
        $request_models = new RequestModels($request, [
            'leaderboard_source',
            'character',
            'release',
            'mode',
            'multiplayer_type',
            'soundtrack'
        ]);

        $player_id = $request->player_id;

        $cache_prefix_name = $request_models->getCacheNamePrefix();

        $cache_key = "player:{$player_id}:leaderboards:daily:" . (string)$cache_prefix_name;

        return DailyLeaderboardsResource::collection(
            Cache::store('opcache')->remember($cache_key, 300, function() use($player_id, $request_models) {
                $records = Leaderboards::getPlayerDailyApiReadQuery(
                    $request_models->leaderboard_source,
                    $player_id,
                    $request_models->character,
                    $request_models->release,
                    $request_models->mode,
                    $request_models->multiplayer_type,
                    $request_models->soundtrack
                )->get();

                Encoder::jsonDecodeProperties($records, [
                    'details'
                ]);

                return $records;
            })
        );
    }
}
