<?php

namespace App\Http\Controllers\Api;

use DateTime;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\LeaderboardEntriesResource;
use App\Http\Requests\Api\ReadLeaderboardEntries;
use App\Http\Requests\Api\ReadDailyLeaderboardEntries;
use App\Components\CacheNames\Leaderboards\Steam as CacheNames;
use App\Components\Dataset\Dataset;
use App\Components\Dataset\Indexes\Sql as SqlIndex;
use App\Components\Dataset\DataProviders\Sql as SqlDataProvider;
use App\LeaderboardEntries;
use App\Releases;
use App\Modes;

class LeaderboardEntriesController extends Controller {
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api')->except([
            'nonDailyIndex',
            'dailyIndex'
        ]);
    }

    /**
     * Display a listing of a non daily leaderboard.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function nonDailyIndex(ReadLeaderboardEntries $request) {
        $index_name = CacheNames::getIndex((int)$request->lbid, []);
        
        
        /* ---------- Data Provider ---------- */
        
        $data_provider = new SqlDataProvider(LeaderboardEntries::getNonDailyApiReadQuery($request->lbid, new DateTime($request->date)));
        
        
        /* ---------- Index ---------- */
        
        $index = new SqlIndex($index_name);
        
        
        /* ---------- Dataset ---------- */
        
        $dataset = new Dataset($index_name, $data_provider);
        
        $dataset->setIndex($index, 'sup.steam_user_id');
        
        $dataset->setIndexSubName($request->date);
        
        $dataset->setFromRequest($request);
        
        $dataset->setSortCallback(function($entry, $key) {
            return $entry->rank;
        });
        
        $dataset->process();
        
        return LeaderboardEntriesResource::collection($dataset->getPaginator());
    }
    
    /**
     * Display a listing of a daily leaderboard.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function dailyIndex(ReadDailyLeaderboardEntries $request) {
        $release_id = Releases::getByName($request->release)->release_id;
        $mode_id = Modes::getByName('normal')->mode_id;
        $date = new DateTime($request->date);
        
        $index_name = CacheNames::getDailyIndex($date, [
            $release_id,
            $mode_id
        ]);
        
        
        /* ---------- Data Provider ---------- */
        
        $data_provider = new SqlDataProvider(LeaderboardEntries::getDailyApiReadQuery($release_id, $date));
        
        
        /* ---------- Index ---------- */
        
        $index = new SqlIndex($index_name);
        
        
        /* ---------- Dataset ---------- */
        
        $dataset = new Dataset($index_name, $data_provider);
        
        $dataset->setIndex($index, 'sup.steam_user_id');
        
        $dataset->setIndexSubName($request->date);
        
        $dataset->setFromRequest($request);
        
        $dataset->setSortCallback(function($entry, $key) {
            return $entry->rank;
        });
        
        $dataset->process();
        
        return LeaderboardEntriesResource::collection($dataset->getPaginator());
    }
}
