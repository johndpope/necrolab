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