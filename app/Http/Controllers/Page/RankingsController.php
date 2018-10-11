<?php

namespace App\Http\Controllers\Page;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RankingsController extends Controller {    
    /**
     * Show the players page.
     *
     * @return \Illuminate\Http\Response
     */
    public function powerIndex() {
        return view('page/rankings/power');
    }
}
