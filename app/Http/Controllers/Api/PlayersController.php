<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\PlayersResource;
use App\Http\Requests\Api\ReadPlayers;
use App\Components\CacheNames\Players as CacheNames;
use App\Components\Dataset\Dataset;
use App\Components\Dataset\Indexes\Sql as SqlIndex;
use App\Components\Dataset\DataProviders\Sql as SqlDataProvider;
use App\Players;

class PlayersController extends Controller {
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
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(ReadPlayers $request) {        
        $index_name = CacheNames::getUsersIndex();
        
        
        /* ---------- Data Provider ---------- */
        
        $data_provider = new SqlDataProvider(Players::getApiReadQuery());
        
        
        /* ---------- Index ---------- */
        
        $index = new SqlIndex($index_name);
        
        
        /* ---------- Dataset ---------- */
        
        $dataset = new Dataset($index_name, $data_provider);
        
        $dataset->setIndex($index, 'su.steam_user_id');
        
        $dataset->setFromRequest($request);
        
        $dataset->process();
        
        return PlayersResource::collection($dataset->getPaginator());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $steamid
     * @return \Illuminate\Http\Response
     */
    public function show($steamid) {
        return new PlayersResource(
            Cache::store('opcache')->remember("steam_users:{$steamid}", 5, function() use($steamid) {
                return Players::getApiReadQuery()
                    ->where('su.steamid', $steamid)
                    ->first();
            })
        );
    }
}
