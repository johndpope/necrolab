<?php

namespace App\Http\Controllers\Api;

use DateTime;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\LeaderboardEntriesResource;
use App\Http\Requests\Api\ReadLeaderboardEntries;
use App\Http\Requests\Api\ReadDailyLeaderboardEntries;
use App\Components\CacheNames\Leaderboards\Steam as CacheNames;
use App\Components\EntriesDataset;
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
        $dataset = new EntriesDataset(
            CacheNames::getIndex((int)$request->lbid, []), 
            'su.steam_user_id', 
            LeaderboardEntries::getNonDailyApiReadQuery($request->lbid, new DateTime($request->date))
        );
        
        $dataset->setIndexSubName($request->date);
        
        $dataset->setFromRequest($request);
        
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
    
        $dataset = new EntriesDataset(
            CacheNames::getDailyIndex($date, [
                $release_id,
                $mode_id
            ]),
            'su.steam_user_id', 
            LeaderboardEntries::getDailyApiReadQuery($release_id, $date)
        );
        
        $dataset->setIndexSubName($request->date);
        
        $dataset->setFromRequest($request);
        
        $dataset->process();
    
        return LeaderboardEntriesResource::collection($dataset->getPaginator());
    }
}