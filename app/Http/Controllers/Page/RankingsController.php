<?php

namespace App\Http\Controllers\Page;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RankingsController extends Controller {    
    /**
     * Show the power rankings page.
     *
     * @return \Illuminate\Http\Response
     */
    public function powerIndex() {
        return view('page/rankings/power');
    }
    
    /**
     * Show the score rankings page.
     *
     * @return \Illuminate\Http\Response
     */
    public function scoreIndex() {
        return view('page/rankings/score');
    }
    
    /**
     * Show the speed rankings page.
     *
     * @return \Illuminate\Http\Response
     */
    public function speedIndex() {
        return view('page/rankings/speed');
    }
    
    /**
     * Show the deathless rankings page.
     *
     * @return \Illuminate\Http\Response
     */
    public function deathlessIndex() {
        return view('page/rankings/deathless');
    }
    
    /**
     * Show the character rankings page.
     *
     * @return \Illuminate\Http\Response
     */
    public function characterIndex() {
        return view('page/rankings/character');
    }
    
    /**
     * Show the daily rankings page.
     *
     * @return \Illuminate\Http\Response
     */
    public function dailyIndex() {
        return view('page/rankings/daily');
    }
}
