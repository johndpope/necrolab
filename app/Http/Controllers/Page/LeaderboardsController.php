<?php

namespace App\Http\Controllers\Page;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LeaderboardsController extends Controller {    
    /**
     * Show the score leaderboards page.
     *
     * @return \Illuminate\Http\Response
     */
    public function scoreIndex() {
        return view('page/leaderboards/score');
    }
    
    /**
     * Show the speed leaderboards page.
     *
     * @return \Illuminate\Http\Response
     */
    public function speedIndex() {
        return view('page/leaderboards/speed');
    }
    
    /**
     * Show the deathless leaderboards page.
     *
     * @return \Illuminate\Http\Response
     */
    public function deathlessIndex() {
        return view('page/leaderboards/deathless');
    }
}
