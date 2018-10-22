<?php

namespace App\Http\Controllers\Page;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LeaderboardEntriesController extends Controller {    
    /**
     * Show the power rankings page.
     *
     * @return \Illuminate\Http\Response
     */
    public function dailyIndex() {
        return view('page/leaderboards/daily_entries');
    }
}
