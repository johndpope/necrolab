<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use App\Http\Resources\LeaderboardSnapshotsResource;
use App\LeaderboardSnapshots;

class LeaderboardSnapshotsController extends Controller {
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
     * Display a listing of all snapshots for this leaderboard.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index($lbid) {      
        return LeaderboardSnapshotsResource::collection(
            Cache::store('opcache')->remember("leaderboards:{$lbid}:snapshots", 5, function() use($lbid) {
                return LeaderboardSnapshots::getApiReadQuery($lbid)->get();
            })
        );
    }
}
