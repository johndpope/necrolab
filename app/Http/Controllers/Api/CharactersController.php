<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CharactersResource;
use App\Characters;

class CharactersController extends Controller {
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api')->except('index');
        
        $this->middleware('permission:characters:store')->only('store');
        $this->middleware('permission:characters:show')->only('show');
        $this->middleware('permission:characters:update')->only('update');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return CharactersResource::collection(
            Characters::getAllFromCache()
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $request->validate(Characters::getValidationRules());
        
        $record = new Characters();
        
        $record->name = $request->input('name');
        $record->display_name = $request->input('display_name');
        $record->is_active = $request->input('is_active');
        $record->sort_order = $request->input('sort_order');
        
        $record->save();
        
        return new CharactersResource($record);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        return new CharactersResource(
            Characters::findOrFail($id)
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
        $validation_rules = Characters::getValidationRules();
        
        // Remove the validation rules for all fields that will not be updated.
        unset($validation_rules['name']);
    
        $request->validate($validation_rules);
        
        $record = Characters::findOrFail($id);
        
        $record->display_name = $request->input('display_name');
        $record->is_active = $request->input('is_active');
        $record->sort_order = $request->input('sort_order');
        
        $record->save();
        
        return new CharactersResource($record);
    }
}
