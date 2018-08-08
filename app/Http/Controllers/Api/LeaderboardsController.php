<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use App\Http\Resources\LeaderboardsResource;
use App\Http\Resources\DailyLeaderboardsResource;
use App\Http\Resources\LeaderboardsXmlResource;
use App\Http\Requests\Api\ReadLeaderboards;
use App\Http\Requests\Api\ReadDeathlessLeaderboards;
use App\Http\Requests\Api\ReadDailyLeaderboards;
use App\Leaderboards;
use App\Releases;
use App\Modes;

class LeaderboardsController extends Controller {
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api')->except([
            'index',
            'scoreIndex',
            'speedIndex',
            'deathlessIndex',
            'dailyIndex',
            'show',
            'xmlIndex'
        ]);
    }

    /**
     * Display a listing of all leaderboards.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(ReadLeaderboards $request) {
        $release_id = Releases::getByName($request->release)->release_id;
        $mode_id = Modes::getByName($request->mode)->mode_id;        
        
        return LeaderboardsResource::collection(
            Cache::store('opcache')->remember("leaderboards:steam:{$release_id}:{$mode_id}", 5, function() use($release_id, $mode_id) {
                return Leaderboards::getNonDailyApiReadQuery(
                    $release_id,
                    $mode_id
                )->get();
            })
        );
    }
    
    /**
     * Display a listing of all score leaderboards.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function scoreIndex(ReadLeaderboards $request) {
        $release_id = Releases::getByName($request->release)->release_id;
        $mode_id = Modes::getByName($request->mode)->mode_id;
        
        return LeaderboardsResource::collection(
            Cache::store('opcache')->remember("leaderboards:steam:score:{$release_id}:{$mode_id}", 5, function() use($release_id, $mode_id) {
                return Leaderboards::getScoreApiReadQuery(
                    $release_id,
                    $mode_id
                )->get();
            })
        );
    }
    
    /**
     * Display a listing of all speed leaderboards.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function speedIndex(ReadLeaderboards $request) {
        $release_id = Releases::getByName($request->release)->release_id;
        $mode_id = Modes::getByName($request->mode)->mode_id;
        
        return LeaderboardsResource::collection(
            Cache::store('opcache')->remember("leaderboards:steam:speed:{$release_id}:{$mode_id}", 5, function() use($release_id, $mode_id) {
                return Leaderboards::getSpeedApiReadQuery(
                    $release_id,
                    $mode_id
                )->get();
            })
        );
    }
    
    /**
     * Display a listing of all deathless leaderboards.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function deathlessIndex(ReadDeathlessLeaderboards $request) {
        $release_id = Releases::getByName($request->release)->release_id;
        
        return LeaderboardsResource::collection(
            Cache::store('opcache')->remember("leaderboards:steam:deathless:{$release_id}", 5, function() use($release_id) {
                return Leaderboards::getDeathlessApiReadQuery(
                    $release_id
                )->get();
            })
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $lbid
     * @return \Illuminate\Http\Response
     */
    public function show($lbid) {
        return new LeaderboardsResource(
            Leaderboards::getApiShowQuery($lbid)->first()
        );
    }
    
    /**
     * Display a listing of all daily leaderboards.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function dailyIndex(ReadDailyLeaderboards $request) {
        $release_id = Releases::getByName($request->release)->release_id;
        
        return DailyLeaderboardsResource::collection(
            Cache::store('opcache')->remember("leaderboards:steam:daily:{$release_id}", 5, function() use($release_id) {
                return Leaderboards::getDailyApiReadQuery(
                    $release_id
                )->get();
            })
        );
    }
    
    /**
     * Display a listing of all leaderboard xml entries.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function xmlIndex() {        
        return LeaderboardsXmlResource::collection(
            Cache::store('opcache')->remember("leaderboards:xml", 5, function() {
                return collect(Leaderboards::getXmlUrls());
            })
        );
    }
}
