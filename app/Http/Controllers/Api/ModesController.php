<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ModesResource;
use App\Modes;

class ModesController extends Controller {
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api')->except('index');
        
        $this->middleware('permission:modes:store')->only('store');
        $this->middleware('permission:modes:show')->only('show');
        $this->middleware('permission:modes:update')->only('update');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return ModesResource::collection(
            Modes::getAllFromCache()
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $request->validate(Modes::getValidationRules());
        
        $record = new Modes();
        
        $record->name = $request->input('name');
        $record->display_name = $request->input('display_name');
        $record->sort_order = $request->input('sort_order');
        
        $record->save();
        
        return new ModesResource($record);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        return new ModesResource(
            Modes::findOrFail($id)
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
        $validation_rules = Modes::getValidationRules();
        
        // Remove the validation rules for all fields that will not be updated.
        unset($validation_rules['name']);
    
        $request->validate($validation_rules);
        
        $record = Modes::findOrFail($id);
        
        $record->display_name = $request->input('display_name');
        $record->sort_order = $request->input('sort_order');
        
        $record->save();
        
        return new ModesResource($record);
    }
}
