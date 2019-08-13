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
use App\LeaderboardEntries;

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
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(ReadPlayerStats $request) {
        $request_models = new RequestModels($request, [
            'leaderboard_source'
        ]);

        $cache_prefix_name = $request_models->getCacheNamePrefix();

        return PlayerStatsResource::collection(
            Cache::store('opcache')->remember(
                "players:stats:total:{$request->player_id}:" . (string)$cache_prefix_name,
                300,
                function() use($request, $request_models) {
                    $stats_history = PlayerStats::getPlayerApiReadQuery(
                        $request->player_id,
                        $request_models->leaderboard_source
                    )->get();

                    if(!$stats_history->isEmpty()) {
                        $latest_history_record = $stats_history->first();
                        $latest_record = clone $latest_history_record;

                        $latest_stats = PlayerStats::getPlayerLatest($request_models->leaderboard_source, $request->player_id);

                        $latest_record->date = $latest_stats['date'];

                        foreach($latest_stats as $field_name => $field_value) {
                            $latest_record->$field_name = $field_value;
                        }

                        if(
                            $latest_record->date != $latest_history_record->date
                        ) {
                            $stats_history->prepend($latest_record);
                        }
                    }

                    return $stats_history;
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
                "players:stats:latest:{$request->player_id}:" . (string)$cache_prefix_name,
                300,
                function() use($request, $request_models) {
                    $stats_record = PlayerStats::getPlayerLatestApiReadQuery(
                        $request->player_id,
                        $request_models->leaderboard_source
                    )->first();

                    $latest_stats = PlayerStats::getPlayerLatest($request_models->leaderboard_source, $request->player_id);

                    foreach($latest_stats as $field_name => $field_value) {
                        $stats_record->$field_name = $field_value;
                    }

                    return $stats_record;
                }
            )
        );
    }

    /**
     * Display all stats for a player by the release specified in the request.
     *
     * @param  \App\Http\Requests\Api\ReadPlayerStatsByRelease $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function byRelease(ReadPlayerStatsByRelease $request) {
        $request_models = new RequestModels($request, [
            'leaderboard_source',
            'release'
        ]);

        $cache_prefix_name = $request_models->getCacheNamePrefix();

        return PlayerStatsByReleaseResource::collection(
            Cache::store('opcache')->remember(
                "players:stats:by_release:{$request->player_id}:" . (string)$cache_prefix_name,
                300,
                function() use($request, $request_models) {
                    $stats_history = PlayerStats::getPlayerByReleaseApiReadQuery(
                        $request->player_id,
                        $request_models->leaderboard_source,
                        $request_models->release
                    )->get();

                    if(!$stats_history->isEmpty()) {
                        $latest_history_record = $stats_history->first();
                        $latest_record = clone $latest_history_record;

                        $latest_stats = PlayerStats::getPlayerLatest(
                            $request_models->leaderboard_source,
                            $request->player_id,
                            $request_models->release
                        );

                        $latest_record->date = $latest_stats['date'];

                        foreach($latest_stats as $field_name => $field_value) {
                            $latest_record->$field_name = $field_value;
                        }

                        if(
                            $latest_record->date != $latest_history_record->date
                        ) {
                            $stats_history->prepend($latest_record);
                        }
                    }

                    return $stats_history;
                }
            )
        );
    }
}
