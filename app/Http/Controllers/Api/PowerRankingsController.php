<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use App\Components\Encoder;
use App\Http\Resources\PowerRankingsResource;
use App\Http\Requests\Api\ReadPowerRankings;
use App\PowerRankings;
use App\Releases;
use App\Modes;
use App\SeededTypes;

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
        $release_id = Releases::getByName($request->release)->id;
        $mode_id = Modes::getByName($request->mode)->id;
        $seeded_type_id = SeededTypes::getByName($request->seeded_type)->id;
        
        return PowerRankingsResource::collection(
            Cache::store('opcache')->remember("rankings:power:steam:{$release_id}:{$mode_id}", 5, function() use($release_id, $mode_id, $seeded_type_id) {
                $query = PowerRankings::getApiReadQuery(
                    $release_id,
                    $mode_id,
                    $seeded_type_id
                );
                
                $records = [];
                
                foreach($query->cursor() as $power_ranking) {
                    if(!empty($power_ranking->categories)) {
                        $power_ranking->categories = Encoder::decode(stream_get_contents($power_ranking->categories));
                    }
                    
                    if($power_ranking->characters) {
                        $power_ranking->characters = Encoder::decode(stream_get_contents($power_ranking->characters));
                    }
                    
                    $records[] = $power_ranking;
                }
                
                return collect($records);
            })
        );
    }
}
