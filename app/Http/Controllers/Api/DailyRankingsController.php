<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
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
            'index',
            'show'
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(ReadDailyRankings $request) {
        $release_id = Releases::getByName($request->release)->id;
        $daily_ranking_day_type_id = DailyRankingDayTypes::getByName($request->number_of_days)->daily_ranking_day_type_id;
        
        return DailyRankingsResource::collection(
            Cache::store('opcache')->remember("rankings:daily:steam:{$release_id}:{$daily_ranking_day_type_id}", 5, function() use($release_id, $daily_ranking_day_type_id) {
                return DailyRankings::getApiReadQuery(
                    $release_id,
                    $daily_ranking_day_type_id
                )->get();
            })
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {}
}
