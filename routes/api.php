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

Route::fallback('Api\HomeController@notFound');


/* --------- Supplemental data endpoints --------- */

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

// Leaderboard Types
Route::get('/1/leaderboards/types', 'Api\LeaderboardTypesController@index');

// Leaderboard Details Columns
Route::get('/1/leaderboards/details_columns', 'Api\LeaderboardDetailsColumnsController@index');

// Leaderboard Sources
Route::get('/1/leaderboards/sources', 'Api\LeaderboardSourcesController@index');

// Data Types
Route::get('/1/data_types', 'Api\DataTypesController@index');


/* --------- All players endpoints --------- */

// Power Rankings
Route::get('/1/rankings/power', 'Api\PowerRankingsController@index');

// Power Ranking Entries
Route::get('/1/rankings/power/entries', 'Api\PowerRankingEntriesController@index');
Route::get('/1/rankings/character/entries', 'Api\PowerRankingEntriesController@characterIndex');
Route::get('/1/rankings/category/entries', 'Api\PowerRankingEntriesController@categoryIndex');

// Daily Rankings Day Types
Route::get('/1/rankings/daily/day_types', 'Api\DailyRankingDayTypesController@index');

// Daily Rankings
Route::get('/1/rankings/daily', 'Api\DailyRankingsController@index');

// Daily Ranking Entries
Route::get('/1/rankings/daily/entries', 'Api\DailyRankingEntriesController@index');

// Leaderboard Entries
Route::get('/1/leaderboard/entries', 'Api\LeaderboardEntriesController@nonDailyIndex');
Route::get('/1/leaderboards/daily/entries', 'Api\LeaderboardEntriesController@dailyIndex');

// Leaderboards
Route::get('/1/leaderboards', 'Api\LeaderboardsController@index');
Route::get('/1/leaderboards/category', 'Api\LeaderboardsController@categoryIndex');
Route::get('/1/leaderboards/characters', 'Api\LeaderboardsController@charactersIndex');
Route::get('/1/leaderboards/daily', 'Api\LeaderboardsController@dailyIndex');
Route::get('/1/leaderboard', 'Api\LeaderboardsController@show');

// Leaderboard Snapshots
Route::get('/1/leaderboard/snapshots', 'Api\LeaderboardSnapshotsController@index');

// Leaderboard
Route::get('/1/leaderboard/by_attributes', 'Api\LeaderboardsController@byAttributes');


/* ---------- Player Endpoints ---------- */

// Players
Route::get('/1/players', 'Api\PlayersController@index');

// Player
Route::get('/1/player', 'Api\PlayersController@show');

// Player Stats
Route::get('/1/player/stats', 'Api\PlayerStatsController@index');
Route::get('/1/player/stats/latest', 'Api\PlayerStatsController@latest');
Route::get('/1/player/stats/by_release', 'Api\PlayerStatsController@byRelease');

// Player Leaderboards
Route::get('/1/player/leaderboards', 'Api\LeaderboardsController@playerIndex');
Route::get('/1/player/leaderboards/category', 'Api\LeaderboardsController@playerCategoryIndex');
Route::get('/1/player/leaderboards/daily', 'Api\LeaderboardsController@playerDailyIndex');

// Player Leaderboard Snapshots
Route::get('/1/player/leaderboard/snapshots', 'Api\LeaderboardSnapshotsController@playerIndex');

// Specific User PBs
Route::get('/1/player/pbs', 'Api\PlayerPbsController@playerIndex');

// Player Power Ranking Entries
Route::get('/1/player/rankings/power/entries', 'Api\PowerRankingEntriesController@playerIndex');
Route::get('/1/player/rankings/character/entries', 'Api\PowerRankingEntriesController@playerCharacterIndex');
Route::get('/1/player/rankings/category/entries', 'Api\PowerRankingEntriesController@playerCategoryIndex');

// Player Daily Ranking Entries
Route::get('/1/player/rankings/daily/entries', 'Api\DailyRankingEntriesController@playerIndex');

// Player Leaderboard Entries
Route::get('/1/player/leaderboards/entries', 'Api\LeaderboardEntriesController@playerNonDailyIndex');
Route::get('/1/player/leaderboards/category/entries', 'Api\LeaderboardEntriesController@playerCategoryIndex');
Route::get('/1/player/leaderboards/daily/entries', 'Api\LeaderboardEntriesController@playerDailyIndex');
