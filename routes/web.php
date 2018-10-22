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
Route::get('/login', 'Page\LoginController@index')->name('login');
Route::get('/login/steam', 'Page\LoginController@loginSteam')->name('login_steam');
Route::get('/login/steam/success', 'Page\LoginController@loginSteamSuccess')->name('login_steam_success');
Route::get('/logout', 'Page\LoginController@logout')->name('logout');

// Players page
Route::get('/players', 'Page\PlayersController@index')->name('players');

// Rankings
Route::get('/rankings/power', 'Page\RankingsController@powerIndex')->name('power_rankings');
Route::get('/rankings/score', 'Page\RankingsController@scoreIndex')->name('score_rankings');
Route::get('/rankings/speed', 'Page\RankingsController@speedIndex')->name('speed_rankings');
Route::get('/rankings/deathless', 'Page\RankingsController@deathlessIndex')->name('deathless_rankings');
Route::get('/rankings/character', 'Page\RankingsController@characterIndex')->name('character_rankings');
Route::get('/rankings/daily', 'Page\RankingsController@dailyIndex')->name('daily_rankings');

// Leaderboards
Route::get('/leaderboards/score', 'Page\LeaderboardsController@scoreIndex')->name('score_leaderboards');
Route::get('/leaderboards/speed', 'Page\LeaderboardsController@speedIndex')->name('speed_leaderboards');
Route::get('/leaderboards/deathless', 'Page\LeaderboardsController@deathlessIndex')->name('deathless_leaderboards');
Route::get('/leaderboards/daily', 'Page\LeaderboardEntriesController@dailyIndex')->name('daily_leaderboard_entries');
