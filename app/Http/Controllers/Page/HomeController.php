<?php

namespace App\Http\Controllers\Page;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HomeController extends Controller {    
    /**
     * Show the home page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view('home');
    }
}
