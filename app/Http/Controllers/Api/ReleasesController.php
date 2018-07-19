<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ReleasesResource;
use App\Releases;

class ReleasesController extends Controller {
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api')->except('index');
        
        $this->middleware('permission:releases:store')->only('store');
        $this->middleware('permission:releases:show')->only('show');
        $this->middleware('permission:releases:update')->only('update');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return ReleasesResource::collection(
            Releases::all()
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $request->validate(Releases::getValidationRules());
        
        $record = new Releases();
        
        $record->name = $request->input('name');
        $record->display_name = $request->input('display_name');
        $record->start_date = $request->input('start_date');
        $record->end_date = $request->input('end_date');
        
        $record->save();
        
        return new ReleasesResource($record);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        return new ReleasesResource(
            Releases::findOrFail($id)
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
        $validation_rules = Releases::getValidationRules();
        
        // Remove the validation rules for all fields that will not be updated.
        unset($validation_rules['name']);
    
        $request->validate($validation_rules);
        
        $record = Releases::findOrFail($id);
        
        $record->display_name = $request->input('display_name');
        $record->start_date = $request->input('start_date');
        $record->end_date = $request->input('end_date');
        
        $record->save();
        
        return new ReleasesResource($record);
    }
}
