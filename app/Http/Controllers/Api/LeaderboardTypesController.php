<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\LeaderboardTypesResource;
use App\LeaderboardTypes;

class LeaderboardTypesController extends Controller {
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api')->except('index');
        
        $this->middleware('permission:leaderboard_types:store')->only('store');
        $this->middleware('permission:leaderboard_types:show')->only('show');
        $this->middleware('permission:leaderboard_types:update')->only('update');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return LeaderboardTypesResource::collection(
            LeaderboardTypes::all()
        );
    }
}
