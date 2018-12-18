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
use App\Http\Requests\Api\ReadSteamUserDailyLeaderboardEntries;
use App\Components\CacheNames\Leaderboards\Steam as CacheNames;
use App\Components\Dataset\Dataset;
use App\Components\Dataset\Indexes\Sql as SqlIndex;
use App\Components\Dataset\DataProviders\Sql as SqlDataProvider;
use App\LeaderboardEntries;
use App\LeaderboardTypes;
use App\Releases;
use App\Modes;
use App\SeededTypes;
use App\Soundtracks;
use App\MultiplayerTypes;

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
            'playerCategoryIndex',
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
        
        $dataset->setIndex($index, 'le.steam_user_id');
        
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
        $mode_id = Modes::getByName($request->mode)->mode_id;
        $date = new DateTime($request->date);
        
        $index_name = CacheNames::getDailyIndex($date, [
            $release_id,
            $mode_id
        ]);
        
        
        /* ---------- Data Provider ---------- */
        
        $data_provider = new SqlDataProvider(LeaderboardEntries::getDailyApiReadQuery($release_id, $mode_id, $date));
        
        
        /* ---------- Index ---------- */
        
        $index = new SqlIndex($index_name);
        
        
        /* ---------- Dataset ---------- */
        
        $dataset = new Dataset($index_name, $data_provider);
        
        $dataset->setIndex($index, 'le.steam_user_id');
        
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
        
        $seeded_type_id = SeededTypes::getByName($validated_request['seeded_type'])->id;
        $multiplayer_type_id = MultiplayerTypes::getByName($validated_request['multiplayer_type'])->id;
        $soundtrack_id = Soundtracks::getByName($validated_request['soundtrack'])->id;
        
        $cache_key = "players:steam:{$steamid}:leaderboards:{$release_id}:{$mode_id}:{$seeded_type_id}:{$multiplayer_type_id}:{$soundtrack_id}:entries:{$date->format('Y-m-d')}";
        
        return LeaderboardEntriesResource::collection(
            Cache::store('opcache')->remember($cache_key, 5, function() use(
                $steamid,
                $date,
                $release_id, 
                $mode_id,
                $seeded_type_id,
                $multiplayer_type_id,
                $soundtrack_id
            ) {
                return LeaderboardEntries::getSteamUserNonDailyApiReadQuery(
                    $steamid,
                    $date,
                    $release_id,
                    $mode_id,
                    $seeded_type_id,
                    $multiplayer_type_id,
                    $soundtrack_id
                )->get();
            })
        );
    }
    
    /**
     * Display a listing of all leaderboard entries of a particular category for a specific player.
     *
     * @param string $steamid
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function playerCategoryIndex($steamid, ReadSteamUserLeaderboardEntries $request) {        
        $validated_request = $request->validated();
 
        $leaderboard_type_id = LeaderboardTypes::getByName($request->leaderboard_type)->leaderboard_type_id;
        $release_id = Releases::getByName($request->release)->release_id;
        $mode_id = Modes::getByName($request->mode)->mode_id;
        $date = new DateTime($request->date);
        
        $seeded_type_id = SeededTypes::getByName($request->seeded_type)->id;
        $multiplayer_type_id = MultiplayerTypes::getByName($request->multiplayer_type)->id;
        $soundtrack_id = Soundtracks::getByName($request->soundtrack)->id;
        
        $cache_key = "players:steam:{$steamid}:leaderboards:{$leaderboard_type_id}:{$release_id}:{$mode_id}:{$seeded_type_id}:{$multiplayer_type_id}:{$soundtrack_id}:score:entries:{$date->format('Y-m-d')}";
        
        return LeaderboardEntriesResource::collection(
            Cache::store('opcache')->remember($cache_key, 5, function() use(
                $steamid,
                $date,
                $leaderboard_type_id,
                $release_id, 
                $mode_id,
                $seeded_type_id,
                $multiplayer_type_id,
                $soundtrack_id
            ) {
                return LeaderboardEntries::getSteamUserCategoryApiReadQuery(
                    $steamid,
                    $date,
                    $leaderboard_type_id,
                    $release_id,
                    $mode_id,
                    $seeded_type_id,
                    $multiplayer_type_id,
                    $soundtrack_id
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
        $mode_id = Modes::getByName($request->mode)->mode_id;
        
        
        /* ---------- Data Provider ---------- */
        
        $data_provider = new SqlDataProvider(LeaderboardEntries::getSteamUserDailyApiReadQuery($steamid, $release_id, $mode_id));
        
        
        /* ---------- Dataset ---------- */
        
        $cache_key = "players:steam:{$steamid}:leaderboards:{$release_id}:{$mode_id}:daily:entries";
        
        $dataset = new Dataset($cache_key, $data_provider);
        
        $dataset->setFromRequest($request);
        
        $dataset->process();
        
        return LeaderboardEntriesResource::collection($dataset->getPaginator());
    }
}
