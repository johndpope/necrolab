<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::fallback(function(){
    return response()->json([], 404);
});

// Releases
Route::get('/1/releases', 'Api\ReleasesController@index');
Route::post('/1/releases', 'Api\ReleasesController@store');
Route::get('/1/releases/{id}', 'Api\ReleasesController@show');
Route::put('/1/releases/{id}', 'Api\ReleasesController@update');

// Modes
Route::get('/1/modes', 'Api\ModesController@index');
Route::post('/1/modes', 'Api\ModesController@store');
Route::get('/1/modes/{id}', 'Api\ModesController@show');
Route::put('/1/modes/{id}', 'Api\ModesController@update');

// Characters
Route::get('/1/characters', 'Api\CharactersController@index');
Route::post('/1/characters', 'Api\CharactersController@store');
Route::get('/1/characters/{id}', 'Api\CharactersController@show');
Route::put('/1/characters/{id}', 'Api\CharactersController@update');

// External Sites
Route::get('/1/external_sites', 'Api\ExternalSitesController@index');
Route::post('/1/external_sites', 'Api\ExternalSitesController@store');
Route::get('/1/external_sites/{id}', 'Api\ExternalSitesController@show');
Route::put('/1/external_sites/{id}', 'Api\ExternalSitesController@update');
Route::put('/1/external_sites/{id}/enable', 'Api\ExternalSitesController@enable');
Route::put('/1/external_sites/{id}/disable', 'Api\ExternalSitesController@disable');

// Steam Players
Route::get('/1/players/steam', 'Api\SteamUsersController@index');
Route::get('/1/players/steam/{id}', 'Api\SteamUsersController@show');

// Steam User PBs
Route::get('/1/players/steam/pbs/score', 'Api\SteamUserPbsController@scoreIndex');
Route::get('/1/players/steam/pbs/speed', 'Api\SteamUserPbsController@speedIndex');
Route::get('/1/players/steam/pbs/deathless', 'Api\SteamUserPbsController@deathlessIndex');

// Specific Steam User PBs
Route::get('/1/players/steam/{steamid}/pbs/score', 'Api\SteamUserPbsController@playerScoreIndex');
Route::get('/1/players/steam/{steamid}/pbs/speed', 'Api\SteamUserPbsController@playerSpeedIndex');
Route::get('/1/players/steam/{steamid}/pbs/deathless', 'Api\SteamUserPbsController@playerDeathlessIndex');

// Power Rankings
Route::get('/1/rankings/power', 'Api\PowerRankingsController@index');

// Power Ranking Entries
Route::get('/1/rankings/power/entries', 'Api\PowerRankingEntriesController@index');
Route::get('/1/rankings/score/entries', 'Api\PowerRankingEntriesController@scoreIndex');
Route::get('/1/rankings/speed/entries', 'Api\PowerRankingEntriesController@speedIndex');
Route::get('/1/rankings/deathless/entries', 'Api\PowerRankingEntriesController@deathlessIndex');
Route::get('/1/rankings/character/entries', 'Api\PowerRankingEntriesController@characterIndex');

// Daily Rankings
Route::get('/1/rankings/daily', 'Api\DailyRankingsController@index');

// Daily Ranking Entries
Route::get('/1/rankings/daily/entries', 'Api\DailyRankingEntriesController@index');

// Leaderboard Entries
Route::get('/1/leaderboards/entries', 'Api\LeaderboardEntriesController@nonDailyIndex');
Route::get('/1/leaderboards/daily/entries', 'Api\LeaderboardEntriesController@dailyIndex');

// Steam User Leaderboard Entries
Route::get('/1/players/steam/{steamid}/leaderboards/entries', 'Api\LeaderboardEntriesController@playerNonDailyIndex');
Route::get('/1/players/steam/{steamid}/leaderboards/score/entries', 'Api\LeaderboardEntriesController@playerScoreIndex');
Route::get('/1/players/steam/{steamid}/leaderboards/speed/entries', 'Api\LeaderboardEntriesController@playerSpeedIndex');
Route::get('/1/players/steam/{steamid}/leaderboards/deathless/entries', 'Api\LeaderboardEntriesController@playerDeathlessIndex');
Route::get('/1/players/steam/{steamid}/leaderboards/daily/entries', 'Api\LeaderboardEntriesController@playerDailyIndex');

// Leaderboards
Route::get('/1/leaderboards', 'Api\LeaderboardsController@index');
Route::get('/1/leaderboards/score', 'Api\LeaderboardsController@scoreIndex');
Route::get('/1/leaderboards/speed', 'Api\LeaderboardsController@speedIndex');
Route::get('/1/leaderboards/deathless', 'Api\LeaderboardsController@deathlessIndex');
Route::get('/1/leaderboards/daily', 'Api\LeaderboardsController@dailyIndex');
Route::get('/1/leaderboards/xml', 'Api\LeaderboardsController@xmlIndex');
Route::get('/1/leaderboards/{lbid}', 'Api\LeaderboardsController@show');

// Steam User Leaderboards
Route::get('/1/players/steam/{steamid}/leaderboards', 'Api\LeaderboardsController@playerIndex');
Route::get('/1/players/steam/{steamid}/leaderboards/score', 'Api\LeaderboardsController@playerScoreIndex');
Route::get('/1/players/steam/{steamid}/leaderboards/speed', 'Api\LeaderboardsController@playerSpeedIndex');
Route::get('/1/players/steam/{steamid}/leaderboards/deathless', 'Api\LeaderboardsController@playerDeathlessIndex');
Route::get('/1/players/steam/{steamid}/leaderboards/daily', 'Api\LeaderboardsController@playerDailyIndex');

// Leaderboard Snapshots
Route::get('/1/leaderboards/{lbid}/snapshots', 'Api\LeaderboardSnapshotsController@index');

// Steam User Leaderboard Snapshots
Route::get('/1/players/steam/{steamid}/leaderboards/{lbid}/snapshots', 'Api\LeaderboardSnapshotsController@playerIndex');
