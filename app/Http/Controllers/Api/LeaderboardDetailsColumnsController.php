<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\LeaderboardDetailsColumnsResource;
use App\LeaderboardDetailsColumns;

class LeaderboardDetailsColumnsController extends Controller {
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api')->except('index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return LeaderboardDetailsColumnsResource::collection(
            LeaderboardDetailsColumns::all()
        );
    }
}
