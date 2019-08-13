<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\Api\ReadPlayers;
use App\Http\Requests\Api\ReadPlayer;
use App\Http\Resources\PlayersResource;
use App\Components\RequestModels;
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
     * @param  \App\Http\Requests\Api\ReadPlayers $request
     * @return \Illuminate\Http\Response
     */
    public function index(ReadPlayers $request) {
        $request_models = new RequestModels($request, [
            'leaderboard_source'
        ]);

        $index_name = CacheNames::getUsersIndex();


        /* ---------- Data Provider ---------- */

        $data_provider = new SqlDataProvider(Players::getApiReadQuery($request_models->leaderboard_source));


        /* ---------- Index ---------- */

        $index = new SqlIndex($request_models->leaderboard_source, $index_name);


        /* ---------- Dataset ---------- */

        $dataset = new Dataset($request_models->leaderboard_source, $index_name, $data_provider);

        $dataset->setIndex($index, 'p.id');

        $dataset->setFromRequest($request);

        $dataset->process();

        return PlayersResource::collection($dataset->getPaginator());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Http\Requests\Api\ReadPlayer $request
     * @return \Illuminate\Http\Response
     */
    public function show(ReadPlayer $request) {
        $request_models = new RequestModels($request, [
            'leaderboard_source'
        ]);

        $cache_key = "{$request_models->leaderboard_source}:players:{$request->player_id}";

        return new PlayersResource(
            Cache::store('opcache')->remember($cache_key, 300, function() use($request, $request_models) {
                return Players::getApiReadQuery($request_models->leaderboard_source)
                    ->where('p.external_id', $request->player_id)
                    ->first();
            })
        );
    }
}
