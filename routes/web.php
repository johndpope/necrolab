<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Home page
Route::get('/', 'Page\HomeController@index')->name('home');

// Login page
/*Route::get('/login/steam', 'Page\LoginController@loginSteam')->name('login_steam');
Route::get('/login/steam/success', 'Page\LoginController@loginSteamSuccess')->name('login_steam_success');*/
Route::get('/logout', 'Page\LoginController@logout')->name('logout');
