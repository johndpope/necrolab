<?php

namespace App\Http\Controllers\Api;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Database\Query\Builder;
use App\Http\Controllers\Controller;
use App\Http\Resources\DailyRankingEntriesResource;
use App\Http\Requests\Api\ReadDailyRankingEntries;
use App\Http\Requests\Api\ReadPlayerDailyRankingEntries;
use App\Components\RequestModels;
use App\Components\CacheNames\Rankings\Daily as CacheNames;
use App\Components\Dataset\Dataset;
use App\Components\Dataset\Indexes\Sql as SqlIndex;
use App\Components\Dataset\DataProviders\Sql as SqlDataProvider;
use App\DailyRankingEntries;
use App\LeaderboardSources;
use App\Dates;
use App\Releases;
use App\Modes;
use App\DailyRankingDayTypes;

class DailyRankingEntriesController extends Controller {
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
     * Display a listing of daily ranking entries.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(ReadDailyRankingEntries $request) {
        $request_models = new RequestModels($request, [
            'leaderboard_source',
            'character',
            'release',
            'mode',
            'multiplayer_type',
            'soundtrack',
            'number_of_days',
            'date'
        ]);
        
        $cache_names_prefix = $request_models->getCacheNamePrefix();
        
        // leaderboard_source and date aren't used for the cache name prefix
        unset($cache_names_prefix->leaderboard_source);
        unset($cache_names_prefix->date);
        
        $index_name = CacheNames::getBase($cache_names_prefix);
        
        
        /* ---------- Data Provider ---------- */
        
        $data_provider = new SqlDataProvider(DailyRankingEntries::getApiReadQuery(
            $request_models->leaderboard_source,
            $request_models->character->id,
            $request_models->release->id, 
            $request_models->mode->id,
            $request_models->multiplayer_type->id,
            $request_models->soundtrack->id,
            $request_models->number_of_days->id, 
            $request_models->date
        ));
        
        
        /* ---------- Index ---------- */
        
        $index = new SqlIndex($request_models->leaderboard_source, $index_name);
        
        
        /* ---------- Dataset ---------- */
        
        $dataset = new Dataset($request_models->leaderboard_source, $index_name, $data_provider);
        
        $dataset->setIndex($index, 'dre.player_id');
        
        $dataset->setIndexSubName($request_models->date->name);
        
        $dataset->setFromRequest($request);
        
        $dataset->setBinaryFields([
            'details'
        ]);
        
        $dataset->setSortCallback(function($entry, $key) {
            return $entry->rank;
        });
        
        $dataset->process();
        
        return DailyRankingEntriesResource::collection($dataset->getPaginator());
    }
    
    /**
     * Display a listing of daily ranking entries for the specified player.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function playerIndex(ReadPlayerDailyRankingEntries $request) {    
        $player_id = $request->player_id;
        
        $request_models = new RequestModels($request, [
            'leaderboard_source',
            'character',
            'release',
            'mode',
            'multiplayer_type',
            'soundtrack',
            'number_of_days'
        ]);
        
        $cache_names_prefix = $request_models->getCacheNamePrefix();
        
        // leaderboard_source and date aren't used for the cache name prefix
        unset($cache_names_prefix->leaderboard_source);
        unset($cache_names_prefix->date);
        
        
        /* ---------- Data Provider ---------- */
        
        $data_provider = new SqlDataProvider(DailyRankingEntries::getPlayerApiReadQuery(
            $request_models->leaderboard_source,
            $player_id,
            $request_models->character->id,
            $request_models->release->id, 
            $request_models->mode->id,
            $request_models->multiplayer_type->id,
            $request_models->soundtrack->id,
            $request_models->number_of_days->id
        ));
        
        
        /* ---------- Dataset ---------- */
        
        $dataset = new Dataset(
            $request_models->leaderboard_source,
            CacheNames::getPlayer($player_id, $cache_names_prefix), 
            $data_provider
        );
        
        $dataset->setFromRequest($request);
        
        $dataset->setBinaryFields([
            'details'
        ]);
        
        $dataset->setSortCallback(function($entry, $key) {
            return 0 - (new DateTime($entry->date))->getTimestamp();
        });
        
        $dataset->process();
        
        return DailyRankingEntriesResource::collection($dataset->getPaginator());
    }
}
