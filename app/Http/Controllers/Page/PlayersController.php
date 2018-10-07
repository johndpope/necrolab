<?php

namespace App\Http\Controllers\Page;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PlayersController extends Controller {    
    /**
     * Show the players page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view('page/players');
    }
}
