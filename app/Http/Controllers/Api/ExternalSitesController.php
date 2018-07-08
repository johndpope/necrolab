<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ExternalSitesResource;
use App\ExternalSites;

class ExternalSitesController extends Controller {
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api')->except('index');
        
        $this->middleware('permission:external_sites:store')->only('store');
        $this->middleware('permission:external_sites:show')->only('show');
        
        $this->middleware('permission:external_sites:update')->only([
            'update',
            'enable',
            'disable'
        ]);
        
        $this->middleware('permission:external_sites:destroy')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return ExternalSitesResource::collection(
            ExternalSites::getAllFromCache()
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $request->validate(ExternalSites::getValidationRules());
        
        $record = new ExternalSites();
        
        $record->name = $request->input('name');
        $record->display_name = $request->input('display_name');
        $record->active = $request->input('active');
        
        $record->save();
        
        return new ExternalSitesResource($record);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        return new ExternalSitesResource(
            ExternalSites::findOrFail($id)
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
        $validation_rules = ExternalSites::getValidationRules();
        
        // Remove the validation rules for all fields that will not be updated.
        unset($validation_rules['name']);
    
        $request->validate($validation_rules);
        
        $record = ExternalSites::findOrFail($id);
        
        $record->display_name = $request->input('display_name');
        $record->active = $request->input('active');
        
        $record->save();
        
        return new ExternalSitesResource($record);
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $record = ExternalSites::findOrFail($id);

        $record->delete();
    }
    
    /**
     * Enable the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function enable($id) {
        $record = ExternalSites::findOrFail($id);
        
        $record->active = 1;

        $record->save();
        
        return new ExternalSitesResource($record);
    }
    
    /**
     * Disable the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function disable($id) {
        $record = ExternalSites::findOrFail($id);
        
        $record->active = 0;

        $record->save();
        
        return new ExternalSitesResource($record);
    }
}
