<?php

namespace App\Http\Controllers\Api;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use App\Http\Resources\LeaderboardEntriesResource;
use App\Http\Requests\Api\ReadLeaderboardEntries;
use App\Http\Requests\Api\ReadDailyLeaderboardEntries;
use App\Http\Requests\Api\ReadSteamUserLeaderboardEntries;
use App\Http\Requests\Api\ReadSteamUserDeathlessLeaderboardEntries;
use App\Http\Requests\Api\ReadSteamUserDailyLeaderboardEntries;
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
            'dailyIndex',
            'playerNonDailyIndex',
            'playerScoreIndex',
            'playerSpeedIndex',
            'playerDeathlessIndex',
            'playerDailyIndex'
        ]);
    }

    /**
     * Display a listing of a non daily leaderboard entries.
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
     * Display a listing of daily leaderboard entries.
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
    
    /**
     * Display a listing of all non daily leaderboard entries for a specific player.
     *
     * @param string $steamid
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function playerNonDailyIndex($steamid, ReadSteamUserLeaderboardEntries $request) {
        $validated_request = $request->validated();
 
        $release_id = Releases::getByName($validated_request['release'])->release_id;
        $mode_id = Modes::getByName($validated_request['mode'])->mode_id;
        $date = new DateTime($validated_request['date']);
        
        $seeded = $validated_request['seeded'];
        $co_op = $validated_request['co_op'];
        $custom = $validated_request['custom'];
        
        $cache_key = "players:steam:{$steamid}:leaderboards:{$release_id}:{$mode_id}:{$seeded}:{$co_op}:{$custom}:entries:{$date->format('Y-m-d')}";
        
        return LeaderboardEntriesResource::collection(
            Cache::store('opcache')->remember($cache_key, 1, function() use(
                $steamid,
                $date,
                $release_id, 
                $mode_id,
                $seeded,
                $co_op,
                $custom
            ) {
                return LeaderboardEntries::getSteamUserNonDailyApiReadQuery(
                    $steamid,
                    $date,
                    $release_id,
                    $mode_id,
                    $seeded,
                    $co_op,
                    $custom
                )->get();
            })
        );
    }
    
    /**
     * Display a listing of all score leaderboard entries for a specific player.
     *
     * @param string $steamid
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function playerScoreIndex($steamid, ReadSteamUserLeaderboardEntries $request) {
        $validated_request = $request->validated();
 
        $release_id = Releases::getByName($validated_request['release'])->release_id;
        $mode_id = Modes::getByName($validated_request['mode'])->mode_id;
        $date = new DateTime($validated_request['date']);
        
        $seeded = $validated_request['seeded'];
        $co_op = $validated_request['co_op'];
        $custom = $validated_request['custom'];
        
        $cache_key = "players:steam:{$steamid}:leaderboards:{$release_id}:{$mode_id}:{$seeded}:{$co_op}:{$custom}:score:entries:{$date->format('Y-m-d')}";
        
        return LeaderboardEntriesResource::collection(
            Cache::store('opcache')->remember($cache_key, 1, function() use(
                $steamid,
                $date,
                $release_id, 
                $mode_id,
                $seeded,
                $co_op,
                $custom
            ) {            
                return LeaderboardEntries::getSteamUserScoreApiReadQuery(
                    $steamid,
                    $date,
                    $release_id,
                    $mode_id,
                    $seeded,
                    $co_op,
                    $custom
                )->get();
            })
        );
    }
    
    /**
     * Display a listing of all speed leaderboard entries for a specific player.
     *
     * @param string $steamid
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function playerSpeedIndex($steamid, ReadSteamUserLeaderboardEntries $request) {
        $validated_request = $request->validated();
 
        $release_id = Releases::getByName($validated_request['release'])->release_id;
        $mode_id = Modes::getByName($validated_request['mode'])->mode_id;
        $date = new DateTime($validated_request['date']);
        
        $seeded = $validated_request['seeded'];
        $co_op = $validated_request['co_op'];
        $custom = $validated_request['custom'];
        
        $cache_key = "players:steam:{$steamid}:leaderboards:{$release_id}:{$mode_id}:{$seeded}:{$co_op}:{$custom}:speed:entries:{$date->format('Y-m-d')}";
        
        return LeaderboardEntriesResource::collection(
            Cache::store('opcache')->remember($cache_key, 1, function() use(
                $steamid,
                $date,
                $release_id, 
                $mode_id,
                $seeded,
                $co_op,
                $custom
            ) {
                return LeaderboardEntries::getSteamUserSpeedApiReadQuery(
                    $steamid,
                    $date,
                    $release_id,
                    $mode_id,
                    $seeded,
                    $co_op,
                    $custom
                )->get();
            })
        );
    }
    
    /**
     * Display a listing of all deathless leaderboard entries for a specific player.
     *
     * @param string $steamid
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function playerDeathlessIndex($steamid, ReadSteamUserDeathlessLeaderboardEntries $request) {
        $validated_request = $request->validated();
 
        $release_id = Releases::getByName($validated_request['release'])->release_id;
        $date = new DateTime($validated_request['date']);
        
        $seeded = $validated_request['seeded'];
        $co_op = $validated_request['co_op'];
        $custom = $validated_request['custom'];
        
        $cache_key = "players:steam:{$steamid}:leaderboards:{$release_id}:{$seeded}:{$co_op}:{$custom}:deathless:entries:{$date->format('Y-m-d')}";
        
        return LeaderboardEntriesResource::collection(
            Cache::store('opcache')->remember($cache_key, 1, function() use(
                $steamid,
                $date,
                $release_id, 
                $seeded,
                $co_op,
                $custom
            ) {
                return LeaderboardEntries::getSteamUserDeathlessApiReadQuery(
                    $steamid,
                    $date,
                    $release_id,
                    $seeded,
                    $co_op,
                    $custom
                )->get();
            })
        );
    }
    
    /**
     * Display a listing of all daily leaderboard entries for a specific player.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function playerDailyIndex($steamid, ReadSteamUserDailyLeaderboardEntries $request) {
        $release_id = Releases::getByName($request->release)->release_id;
        $start_date = new DateTime($request->start_date);
        $end_date = new DateTime($request->end_date);
        
        
        /* ---------- Data Provider ---------- */
        
        $data_provider = new SqlDataProvider(LeaderboardEntries::getSteamUserDailyApiReadQuery($steamid, $release_id, $start_date, $end_date));
        
        
        /* ---------- Dataset ---------- */
        
        $cache_key = "players:steam:{$steamid}:leaderboards:{$release_id}:daily:entries:{$start_date->format('Y-m-d')}:{$end_date->format('Y-m-d')}";
        
        $dataset = new Dataset($cache_key, $data_provider);
        
        $dataset->setFromRequest($request);
        
        $dataset->process();
        
        return LeaderboardEntriesResource::collection($dataset->getPaginator());
    }
}
