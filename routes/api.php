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

// Modes
Route::get('/1/modes', 'Api\ModesController@index');

// Characters
Route::get('/1/characters', 'Api\CharactersController@index');

// External Sites
Route::get('/1/external_sites', 'Api\ExternalSitesController@index');

// Seeded Types
Route::get('/1/seeded_types', 'Api\SeededTypesController@index');

// Multiplayer Types
Route::get('/1/multiplayer_types', 'Api\MultiplayerTypesController@index');

// Soundtracks
Route::get('/1/soundtracks', 'Api\SoundtracksController@index');

// Data Types
Route::get('/1/data_types', 'Api\DataTypesController@index');

// Steam Players
Route::get('/1/players/steam', 'Api\SteamUsersController@index');
Route::get('/1/players/steam/{id}', 'Api\SteamUsersController@show');

// Specific User PBs
Route::get('/1/players/steam/{steamid}/pbs', 'Api\SteamUserPbsController@playerIndex');

// Power Rankings
Route::get('/1/rankings/power', 'Api\PowerRankingsController@index');

// Power Ranking Entries
Route::get('/1/rankings/power/entries', 'Api\PowerRankingEntriesController@index');
Route::get('/1/rankings/character/entries', 'Api\PowerRankingEntriesController@characterIndex');
Route::get('/1/rankings/category/entries', 'Api\PowerRankingEntriesController@categoryIndex');

// Steam User Power Ranking Entries
Route::get('/1/players/steam/{steamid}/rankings/power/entries', 'Api\PowerRankingEntriesController@playerIndex');
Route::get('/1/players/steam/{steamid}/rankings/character/entries', 'Api\PowerRankingEntriesController@playerCharacterIndex');
Route::get('/1/players/steam/{steamid}/rankings/category/entries', 'Api\PowerRankingEntriesController@playerCategoryIndex');

// Daily Rankings Day Types
Route::get('/1/rankings/daily/day_types', 'Api\DailyRankingDayTypesController@index');

// Daily Rankings
Route::get('/1/rankings/daily', 'Api\DailyRankingsController@index');

// Daily Ranking Entries
Route::get('/1/rankings/daily/entries', 'Api\DailyRankingEntriesController@index');

// Steam User Daily Ranking Entries
Route::get('/1/players/steam/{steamid}/rankings/daily/entries', 'Api\DailyRankingEntriesController@playerIndex');

// Leaderboard Entries
Route::get('/1/leaderboards/entries', 'Api\LeaderboardEntriesController@nonDailyIndex');
Route::get('/1/leaderboards/daily/entries', 'Api\LeaderboardEntriesController@dailyIndex');

// Steam User Leaderboard Entries
Route::get('/1/players/steam/{steamid}/leaderboards/entries', 'Api\LeaderboardEntriesController@playerNonDailyIndex');
Route::get('/1/players/steam/{steamid}/leaderboards/category/entries', 'Api\LeaderboardEntriesController@playerCategoryIndex');
Route::get('/1/players/steam/{steamid}/leaderboards/daily/entries', 'Api\LeaderboardEntriesController@playerDailyIndex');

// Leaderboard Types
Route::get('/1/leaderboards/types', 'Api\LeaderboardTypesController@index');

// Leaderboard Details Columns
Route::get('/1/leaderboards/details_columns', 'Api\LeaderboardDetailsColumnsController@index');

// Leaderboard Sources
Route::get('/1/leaderboards/sources', 'Api\LeaderboardSourcesController@index');

// Leaderboards
Route::get('/1/leaderboards', 'Api\LeaderboardsController@index');
Route::get('/1/leaderboards/category', 'Api\LeaderboardsController@categoryIndex');
Route::get('/1/leaderboards/daily', 'Api\LeaderboardsController@dailyIndex');
Route::get('/1/leaderboards/xml', 'Api\LeaderboardsController@xmlIndex');
Route::get('/1/leaderboards/by_url_name/{url_name}', 'Api\LeaderboardsController@byUrlName');
Route::get('/1/leaderboards/{lbid}', 'Api\LeaderboardsController@show');

// Steam User Leaderboards
Route::get('/1/players/steam/{steamid}/leaderboards', 'Api\LeaderboardsController@playerIndex');
Route::get('/1/players/steam/{steamid}/leaderboards/category', 'Api\LeaderboardsController@playerCategoryIndex');
Route::get('/1/players/steam/{steamid}/leaderboards/daily', 'Api\LeaderboardsController@playerDailyIndex');

// Leaderboard Snapshots
Route::get('/1/leaderboards/{lbid}/snapshots', 'Api\LeaderboardSnapshotsController@index');

// Steam User Leaderboard Snapshots
Route::get('/1/players/steam/{steamid}/leaderboards/{lbid}/snapshots', 'Api\LeaderboardSnapshotsController@playerIndex');
