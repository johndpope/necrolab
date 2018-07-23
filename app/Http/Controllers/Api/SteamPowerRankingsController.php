<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use App\Http\Resources\PowerRankingsResource;
use App\Http\Requests\Api\ReadPowerRankings;
use App\PowerRankings;
use App\Releases;
use App\Modes;

class SteamPowerRankingsController extends Controller {
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
    public function index(ReadPowerRankings $request) {
        $release_id = Releases::getByName($request->release)->release_id;
        $mode_id = Modes::getByName($request->mode)->mode_id;
        $seeded = $request->seeded;
        
        return PowerRankingsResource::collection(
            Cache::store('opcache')->remember("rankings:power:steam:{$release_id}:{$mode_id}", 5, function() use($release_id, $mode_id, $seeded) {
                return PowerRankings::getApiReadQuery(
                    $release_id,
                    $mode_id,
                    $seeded
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
