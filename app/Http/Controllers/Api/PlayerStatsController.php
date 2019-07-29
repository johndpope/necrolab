<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\Api\ReadPlayerStats;
use App\Http\Requests\Api\ReadPlayerStatsByRelease;
use App\Http\Resources\PlayerStatsByReleaseResource;
use App\Http\Resources\PlayerStatsResource;
use App\Components\RequestModels;
use App\PlayerStats;

class PlayerStatsController extends Controller {
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api')->except([
            'index',
            'latest',
            'byRelease'
        ]);
    }

    /**
     * Display all stats for a player.
     *
     * @param  \App\Http\Requests\Api\ReadPlayerStats $request
     * @return \App\Http\Resources\PlayerStatsResource
     */
    public function index(ReadPlayerStats $request) {
        $request_models = new RequestModels($request, [
            'leaderboard_source'
        ]);

        $cache_prefix_name = $request_models->getCacheNamePrefix();

        return PlayerStatsResource::collection(
            Cache::store('opcache')->remember(
                "players:stats:total:" . (string)$cache_prefix_name,
                5,
                function() use($request, $request_models) {
                    return PlayerStats::getPlayerApiReadQuery(
                        $request->player_id,
                        $request_models->leaderboard_source
                    )->get();
                }
            )
        );
    }

    /**
     * Display the latest stats for a player
     *
     * @param  \App\Http\Requests\Api\ReadPlayerStats $request
     * @return \App\Http\Resources\PlayerStatsResource
     */
    public function latest(ReadPlayerStats $request) {
        $request_models = new RequestModels($request, [
            'leaderboard_source'
        ]);

        $cache_prefix_name = $request_models->getCacheNamePrefix();

        return new PlayerStatsResource(
            Cache::store('opcache')->remember(
                "players:stats:latest:" . (string)$cache_prefix_name,
                5,
                function() use($request, $request_models) {
                    return PlayerStats::getPlayerLatestApiReadQuery(
                        $request->player_id,
                        $request_models->leaderboard_source
                    )->first();
                }
            )
        );
    }

    /**
     * Display all stats for a player by the release specified in the request.
     *
     * @param  \App\Http\Requests\Api\ReadPlayerStatsByRelease $request
     * @return \App\Http\Resources\PlayerStatsByReleaseResource
     */
    public function byRelease(ReadPlayerStatsByRelease $request) {
        $request_models = new RequestModels($request, [
            'leaderboard_source',
            'release'
        ]);

        $cache_prefix_name = $request_models->getCacheNamePrefix();

        return PlayerStatsByReleaseResource::collection(
            Cache::store('opcache')->remember(
                "players:stats:by_release:" . (string)$cache_prefix_name,
                5,
                function() use($request, $request_models) {
                    return PlayerStats::getPlayerByReleaseApiReadQuery(
                        $request->player_id,
                        $request_models->leaderboard_source,
                        $request_models->release
                    )->get();
                }
            )
        );
    }
}
