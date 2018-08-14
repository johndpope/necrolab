<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\SteamUsersResource;
use App\Http\Requests\Api\ReadSteamUsers;
use App\Components\CacheNames\Users\Steam as SteamUsersCacheNames;
use App\Components\Dataset\Dataset;
use App\Components\Dataset\Indexes\Sql as SqlIndex;
use App\Components\Dataset\DataProviders\Sql as SqlDataProvider;
use App\SteamUsers;

class SteamUsersController extends Controller {
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api')->except([
            'index',
            'show'
        ]);
        
        $this->middleware('permission:steam_users:store')->only('store');
        $this->middleware('permission:steam_users:update')->only('update');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(ReadSteamUsers $request) {        
        $index_name = SteamUsersCacheNames::getUsersIndex();
        
        
        /* ---------- Data Provider ---------- */
        
        $data_provider = new SqlDataProvider(SteamUsers::getApiReadQuery());
        
        
        /* ---------- Index ---------- */
        
        $index = new SqlIndex($index_name);
        
        
        /* ---------- Dataset ---------- */
        
        $dataset = new Dataset($index_name, $data_provider);
        
        $dataset->setIndex($index, 'su.steam_user_id');
        
        $dataset->setFromRequest($request);
        
        $dataset->process();
        
        return SteamUsersResource::collection($dataset->getPaginator());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $request->validate(SteamUsers::getValidationRules());
        
        $record = new SteamUsers();
        
        $record->name = $request->input('name');
        $record->display_name = $request->input('display_name');
        $record->sort_order = $request->input('sort_order');
        
        $record->save();
        
        return new SteamUsersResource($record);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $steamid
     * @return \Illuminate\Http\Response
     */
    public function show($steamid) {
        return new SteamUsersResource(
            SteamUsers::getApiReadQuery()->where('su.steamid', $steamid)->first()
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $validation_rules = SteamUsers::getValidationRules();
        
        // Remove the validation rules for all fields that will not be updated.
        unset($validation_rules['name']);
    
        $request->validate($validation_rules);
        
        $record = SteamUsers::findOrFail($id);
        
        $record->display_name = $request->input('display_name');
        $record->sort_order = $request->input('sort_order');
        
        $record->save();
        
        return new SteamUsersResource($record);
    }
}
