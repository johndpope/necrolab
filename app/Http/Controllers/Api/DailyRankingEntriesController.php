<?php

namespace App\Http\Controllers\Api;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Database\Query\Builder;
use App\Http\Controllers\Controller;
use App\Http\Resources\DailyRankingEntriesResource;
use App\Http\Requests\Api\ReadDailyRankingEntries;
use App\Http\Requests\Api\ReadPlayerDailyRankingEntries;
use App\Components\CacheNames\Rankings\Daily as CacheNames;
use App\Components\Dataset\Dataset;
use App\Components\Dataset\Indexes\Sql as SqlIndex;
use App\Components\Dataset\DataProviders\Sql as SqlDataProvider;
use App\DailyRankingEntries;
use App\LeaderboardSources;
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
            'index',
            'playerIndex'
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
        
        $index_name = CacheNames::getRankings($release_id, $mode_id, $daily_ranking_day_type_id);
        
        
        /* ---------- Data Provider ---------- */
        
        $data_provider = new SqlDataProvider(DailyRankingEntries::getApiReadQuery(
            $release_id, 
            $mode_id, 
            $daily_ranking_day_type_id, 
            $date
        ));
        
        
        /* ---------- Index ---------- */
        
        $index = new SqlIndex($index_name);
        
        
        /* ---------- Dataset ---------- */
        
        $dataset = new Dataset($index_name, $data_provider);
        
        $dataset->setIndex($index, 'su.steam_user_id');
        
        $dataset->setIndexSubName($request->date);
        
        $dataset->setFromRequest($request);
        
        $dataset->setBinaryFields([
            'characters'
        ]);
        
        $dataset->setSortCallback(function($entry, $key) {
            return $entry->rank;
        });
        
        $dataset->process();
        
        return DailyRankingEntriesResource::collection($dataset->getPaginator());
    }
    
    /**
     * Display a listing of daily ranking entries for the specified player.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function playerIndex(ReadPlayerDailyRankingEntries $request) {
        $leaderboard_source = LeaderboardSources::getByName($request->leaderboard_source);
    
        $player_id = $request->player_id;
        $release_id = Releases::getByName($request->release)->release_id;
        $mode_id = Modes::getByName($request->mode)->mode_id;
        $daily_ranking_day_type_id = DailyRankingDayTypes::getByName($request->number_of_days)->daily_ranking_day_type_id;
        
        
        /* ---------- Data Provider ---------- */
        
        $data_provider = new SqlDataProvider(DailyRankingEntries::getPlayerApiReadQuery(
            $player_id,
            $leaderboard_source,
            $release_id, 
            $mode_id, 
            $daily_ranking_day_type_id
        ));
        
        
        /* ---------- Dataset ---------- */
        
        $dataset = new Dataset(
            CacheNames::getPlayerRankings(
                $player_id, 
                $leaderboard_source->id,
                $release_id, 
                $mode_id, 
                $daily_ranking_day_type_id
            ), 
            $data_provider
        );
        
        $dataset->setFromRequest($request);
        
        $dataset->setSortCallback(function($entry, $key) {
            return 0 - (new DateTime($entry->date))->getTimestamp();
        });
        
        $dataset->process();
        
        return DailyRankingEntriesResource::collection($dataset->getPaginator());
    }
}
