<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use App\Components\RequestModels;
use App\Components\Encoder;
use App\Http\Resources\DailyRankingsResource;
use App\Http\Requests\Api\ReadDailyRankings;
use App\DailyRankings;
use App\Releases;
use App\DailyRankingDayTypes;

class DailyRankingsController extends Controller {
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api')->except([
            'index'
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(ReadDailyRankings $request) {
        $request_models = new RequestModels($request, [
            'leaderboard_source',
            'character',
            'release',
            'mode',
            'multiplayer_type',
            'soundtrack',
            'number_of_days'
        ]);
        
        $cache_prefix_name = $request_models->getCacheNamePrefix();
        
        $cache_name = "rankings:daily:" . (string)$cache_prefix_name;
        
        return DailyRankingsResource::collection(
            Cache::store('opcache')->remember(
                $cache_name, 5, 
                function() use($request_models) {
                    $records = DailyRankings::getApiReadQuery(
                        $request_models->leaderboard_source,
                        $request_models->character,
                        $request_models->release,
                        $request_models->mode,
                        $request_models->multiplayer_type,
                        $request_models->soundtrack,
                        $request_models->number_of_days
                    )->get();
                    
                    Encoder::jsonDecodeProperties($records, [
                        'details'
                    ]);
                    
                    return $records;
                }
            )
        );
    }
}
