<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use App\Http\Resources\LeaderboardEntriesResource;
use App\Http\Requests\Api\ReadLeaderboardEntries;
use App\Http\Requests\Api\ReadDailyLeaderboardEntries;
use App\Http\Requests\Api\ReadPlayerLeaderboardEntries;
use App\Http\Requests\Api\ReadPlayerCategoryLeaderboardEntries;
use App\Http\Requests\Api\ReadPlayerDailyLeaderboardEntries;
use App\Components\RequestModels;
use App\Components\CacheNames\Leaderboards as CacheNames;
use App\Components\Dataset\Dataset;
use App\Components\Dataset\Indexes\Sql as SqlIndex;
use App\Components\Dataset\DataProviders\Sql as SqlDataProvider;
use App\LeaderboardEntries;
use App\LeaderboardSources;
use App\LeaderboardTypes;
use App\Characters;
use App\Releases;
use App\Modes;
use App\SeededTypes;
use App\Soundtracks;
use App\MultiplayerTypes;

class LeaderboardEntriesController extends Controller {
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api')->except([
            'nonDailyIndex',
            'dailyIndex',
            'playerNonDailyIndex',
            'playerCategoryIndex',
            'playerDailyIndex'
        ]);
    }

    /**
     * Display a listing of a non daily leaderboard entries.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function nonDailyIndex(ReadLeaderboardEntries $request) {
        $request_models = new RequestModels($request, [
            'leaderboard_source',
            'date'
        ]);
    
        $index_name = CacheNames::getIndex($request->leaderboard_id, []);
        
        /* ---------- Data Provider ---------- */
        
        $data_provider = new SqlDataProvider(LeaderboardEntries::getNonDailyApiReadQuery(
            $request_models->leaderboard_source,
            $request->leaderboard_id, 
            $request_models->date
        ));
        
        
        /* ---------- Index ---------- */
        
        $index = new SqlIndex($request_models->leaderboard_source, $index_name);
        
        
        /* ---------- Dataset ---------- */
        
        $dataset = new Dataset($request_models->leaderboard_source, $index_name, $data_provider);
        
        $dataset->setIndex($index, 'le.player_id');
        
        $dataset->setIndexSubName($request_models->date->name);
        
        $dataset->setFromRequest($request);
        
        $dataset->setSortCallback(function($entry, $key) {
            return $entry->rank;
        });
        
        $dataset->process();
        
        return LeaderboardEntriesResource::collection($dataset->getPaginator());
    }
    
    /**
     * Display a listing of daily leaderboard entries.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function dailyIndex(ReadDailyLeaderboardEntries $request) {
        $request_models = new RequestModels($request, [
            'leaderboard_source',
            'character',
            'release',
            'mode',
            'multiplayer_type',
            'soundtrack',
            'date'
        ]);
        
        
        /* ---------- Cache Name ---------- */
        
        $cache_prefix_name = $request_models->getCacheNamePrefix();
        
        unset($cache_prefix_name->leaderboard_source);
        unset($cache_prefix_name->date);
        
        $index_name = CacheNames::getDailyEntries($cache_prefix_name);
        
        
        /* ---------- Data Provider ---------- */
        
        $data_provider = new SqlDataProvider(LeaderboardEntries::getDailyApiReadQuery(            
            $request_models->leaderboard_source,
            $request_models->character->id,
            $request_models->release->id,
            $request_models->mode->id,
            $request_models->multiplayer_type->id,
            $request_models->soundtrack->id,
            $request_models->date
        ));
        
        
        /* ---------- Index ---------- */
        
        $index = new SqlIndex($request_models->leaderboard_source, $index_name);
        
        
        /* ---------- Dataset ---------- */
        
        $dataset = new Dataset($request_models->leaderboard_source, $index_name, $data_provider);
        
        $dataset->setIndex($index, 'le.player_id');
        
        $dataset->setIndexSubName($request_models->date->name);
        
        $dataset->setFromRequest($request);
        
        $dataset->setSortCallback(function($entry, $key) {
            return $entry->rank;
        });
        
        $dataset->process();
        
        return LeaderboardEntriesResource::collection($dataset->getPaginator());
    }
    
    /**
     * Display a listing of all non daily leaderboard entries for a specific player.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function playerNonDailyIndex(ReadPlayerLeaderboardEntries $request) {
        $request_models = new RequestModels($request, [
            'leaderboard_source',
            'release',
            'mode',
            'seeded_type',
            'multiplayer_type',
            'soundtrack',
            'date'
        ]);
        
        $player_id = $request->player_id;
        
        $cache_prefix_name = $request_models->getCacheNamePrefix();
        
        $cache_key = "players:{$player_id}:leaderboards:" . (string)$cache_prefix_name . ":entries";
        
        return LeaderboardEntriesResource::collection(
            Cache::store('opcache')->remember($cache_key, 5, function() use(
                $player_id,
                $request_models
            ) {
                return LeaderboardEntries::getPlayerNonDailyApiReadQuery(
                    $player_id,
                    $request_models->leaderboard_source,
                    $request_models->release->id,
                    $request_models->mode->id,
                    $request_models->seeded_type->id,
                    $request_models->multiplayer_type->id,
                    $request_models->soundtrack->id,
                    $request_models->date
                )->get();
            })
        );
    }
    
    /**
     * Display a listing of all leaderboard entries of a particular category for a specific player.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function playerCategoryIndex(ReadPlayerCategoryLeaderboardEntries $request) {
        $request_models = new RequestModels($request, [
            'leaderboard_source',
            'leaderboard_type',
            'release',
            'mode',
            'seeded_type',
            'multiplayer_type',
            'soundtrack',
            'date'
        ]);
        
        $player_id = $request->player_id;
        
        $cache_prefix_name = $request_models->getCacheNamePrefix();
        
        $cache_key = "players:{$player_id}:leaderboards:" . (string)$cache_prefix_name . ":entries";

        return LeaderboardEntriesResource::collection(
            Cache::store('opcache')->remember($cache_key, 5, function() use(
                $player_id,
                $request_models
            ) {
                return LeaderboardEntries::getPlayerCategoryApiReadQuery(
                    $player_id,
                    $request_models->leaderboard_source,
                    $request_models->leaderboard_type->id,
                    $request_models->release->id,
                    $request_models->mode->id,
                    $request_models->seeded_type->id,
                    $request_models->multiplayer_type->id,
                    $request_models->soundtrack->id,
                    $request_models->date
                )->get();
            })
        );
    }
    
    /**
     * Display a listing of all daily leaderboard entries for a specific player.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function playerDailyIndex(ReadPlayerDailyLeaderboardEntries $request) {
        $request_models = new RequestModels($request, [
            'leaderboard_source',
            'character',
            'release',
            'mode',
            'multiplayer_type',
            'soundtrack'
        ]);
        
        $player_id = $request->player_id;
        
        
        /* ---------- Cache Name ---------- */
        
        $cache_prefix_name = $request_models->getCacheNamePrefix();
        
        unset($cache_prefix_name->leaderboard_source);
        
        $index_name = CacheNames::getDailyEntries($cache_prefix_name);
        
        
        /* ---------- Data Provider ---------- */
        
        $data_provider = new SqlDataProvider(LeaderboardEntries::getPlayerDailyApiReadQuery(
            $player_id, 
            $request_models->leaderboard_source,
            $request_models->character->id,
            $request_models->release->id, 
            $request_models->mode->id,
            $request_models->multiplayer_type->id,
            $request_models->soundtrack->id
        ));
        
        
        /* ---------- Dataset ---------- */
        
        $cache_key = "players:{$player_id}:leaderboards:daily:" . (string)$cache_prefix_name . ":entries";
        
        $dataset = new Dataset($request_models->leaderboard_source, $cache_key, $data_provider);
        
        $dataset->setFromRequest($request);
        
        $dataset->process();
        
        return LeaderboardEntriesResource::collection($dataset->getPaginator());
    }
}
