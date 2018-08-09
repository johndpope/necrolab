<?php

namespace App\Http\Controllers\Api;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Database\Query\Builder;
use App\Http\Controllers\Controller;
use App\Http\Resources\DailyRankingEntriesResource;
use App\Http\Requests\Api\ReadDailyRankingEntries;
use App\Components\CacheNames\Rankings\Daily as CacheNames;
use App\Components\EntriesDataset;
use App\DailyRankingEntries;
use App\Releases;
use App\Modes;
use App\DailyRankingDayTypes;

class DailyRankingEntriesController extends Controller {
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api')->except([
            'index'
        ]);
    }

    /**
     * Display a listing of daily ranking entries.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(ReadDailyRankingEntries $request) {
        $release_id = Releases::getByName($request->release)->release_id;
        $mode_id = Modes::getByName($request->mode)->mode_id;
        $daily_ranking_day_type_id = DailyRankingDayTypes::getByName($request->number_of_days)->daily_ranking_day_type_id;
        $date = new DateTime($request->date);
        
        $dataset = new EntriesDataset(
            CacheNames::getRankings($release_id, $mode_id, $daily_ranking_day_type_id), 
            'su.steam_user_id', 
            DailyRankingEntries::getApiReadQuery($release_id, $mode_id, $daily_ranking_day_type_id, $date)
        );
        
        $dataset->setIndexSubName($request->date);
        
        $dataset->setFromRequest($request);
            
        $dataset->setSortCallback(function($entry, $key) {
            return $entry->rank;
        });
        
        $dataset->process();
    
        return DailyRankingEntriesResource::collection($dataset->getPaginator());
    }
}
