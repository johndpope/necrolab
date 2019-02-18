<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use App\Components\RequestModels;
use App\Http\Resources\PowerRankingsResource;
use App\Http\Requests\Api\ReadPowerRankings;
use App\PowerRankings;

class PowerRankingsController extends Controller {
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
    public function index(ReadPowerRankings $request) {
        $request_models = new RequestModels($request, [
            'leaderboard_source',
            'release',
            'mode',
            'seeded_type',
            'multiplayer_type',
            'soundtrack'
        ]);
        
        $cache_prefix_name = $request_models->getCacheNamePrefix();
        
        $cache_name = "rankings:power:" . (string)$cache_prefix_name;
        
        return PowerRankingsResource::collection(
            Cache::store('opcache')->remember(
                $cache_name, 
                5, 
                function() use($request_models) {
                    $records = PowerRankings::getApiReadQuery(
                        $request_models->leaderboard_source,
                        $request_models->release,
                        $request_models->mode,
                        $request_models->seeded_type,
                        $request_models->multiplayer_type,
                        $request_models->soundtrack
                    )->get();
                    
                    if(!empty($records)) {
                        foreach($records as $record) {
                            $record->categories = json_decode($record->categories, true);
                            $record->characters = json_decode($record->characters, true);
                        }
                    }
                    
                    return $records;
                })
        );
    }
}
