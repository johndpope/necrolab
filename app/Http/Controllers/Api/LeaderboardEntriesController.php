<?php

namespace App\Http\Controllers\Api;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use App\Http\Resources\LeaderboardEntriesResource;
use App\Http\Requests\Api\ReadLeaderboardEntries;
use App\Http\Requests\Api\ReadDailyLeaderboardEntries;
use App\Http\Requests\Api\ReadPlayerLeaderboardEntries;
use App\Http\Requests\Api\ReadPlayerCategoryLeaderboardEntries;
use App\Http\Requests\Api\ReadPlayerDailyLeaderboardEntries;
use App\Components\CacheNames\Leaderboards\Steam as CacheNames;
use App\Components\Dataset\Dataset;
use App\Components\Dataset\Indexes\Sql as SqlIndex;
use App\Components\Dataset\DataProviders\Sql as SqlDataProvider;
use App\LeaderboardEntries;
use App\LeaderboardSources;
use App\LeaderboardTypes;
use App\Characters;
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
        $leaderboard_source = LeaderboardSources::getByName($request->leaderboard_source);
    
        $index_name = CacheNames::getIndex($request->leaderboard_id, [
            $leaderboard_source->name
        ]);
        
        /* ---------- Data Provider ---------- */
        
        $data_provider = new SqlDataProvider(LeaderboardEntries::getNonDailyApiReadQuery(
            $leaderboard_source,
            $request->leaderboard_id, 
            new DateTime($request->date)
        ));
        
        
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
        $leaderboard_source_id = LeaderboardSources::getByName($request->leaderboard_source)->id;
        $character_id = Characters::getByName($request->character)->id;
        $release_id = Releases::getByName($request->release)->id;
        $mode_id = Modes::getByName($request->mode)->id;
        $multiplayer_type_id = MultiplayerTypes::getByName($request->multiplayer_type)->id;
        $date = new DateTime($request->date);
        
        $index_name = CacheNames::getDailyIndex($date, [
            $leaderboard_source_id,
            $character_id,
            $release_id,
            $mode_id,
            $multiplayer_type_id
        ]);
        
        
        /* ---------- Data Provider ---------- */
        
        $data_provider = new SqlDataProvider(LeaderboardEntries::getDailyApiReadQuery(
            $leaderboard_source_id,
            $character_id,
            $release_id, 
            $mode_id, 
            $multiplayer_type_id,
            $date
        ));
        
        
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function playerNonDailyIndex(ReadPlayerLeaderboardEntries $request) {
        $leaderboard_source = LeaderboardSources::getByName($request->leaderboard_source);
    
        $player_id = $request->player_id;
        $release_id = Releases::getByName($request->release)->id;
        $mode_id = Modes::getByName($request->mode)->id;        
        $seeded_type_id = SeededTypes::getByName($request->seeded_type)->id;
        $multiplayer_type_id = MultiplayerTypes::getByName($request->multiplayer_type)->id;
        $soundtrack_id = Soundtracks::getByName($request->soundtrack)->id;
        
        $date = new DateTime($request->date);
        
        $cache_key = "players:{$leaderboard_source->name}:{$player_id}:leaderboards:{$release_id}:{$mode_id}:{$seeded_type_id}:{$multiplayer_type_id}:{$soundtrack_id}:entries:{$date->format('Y-m-d')}";
        
        return LeaderboardEntriesResource::collection(
            Cache::store('opcache')->remember($cache_key, 5, function() use(
                $player_id,
                $date,
                $leaderboard_source,
                $release_id, 
                $mode_id,
                $seeded_type_id,
                $multiplayer_type_id,
                $soundtrack_id
            ) {
                return LeaderboardEntries::getPlayerNonDailyApiReadQuery(
                    $player_id,
                    $date,
                    $leaderboard_source,
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function playerCategoryIndex(ReadPlayerCategoryLeaderboardEntries $request) {        
        $leaderboard_source = LeaderboardSources::getByName($request->leaderboard_source);
 
        $player_id = $request->player_id;
        $leaderboard_type_id = LeaderboardTypes::getByName($request->leaderboard_type)->id;
        $release_id = Releases::getByName($request->release)->id;
        $mode_id = Modes::getByName($request->mode)->id;
        $date = new DateTime($request->date);
        
        $seeded_type_id = SeededTypes::getByName($request->seeded_type)->id;
        $multiplayer_type_id = MultiplayerTypes::getByName($request->multiplayer_type)->id;
        $soundtrack_id = Soundtracks::getByName($request->soundtrack)->id;
        
        $cache_key = "players:{$leaderboard_source->name}:{$player_id}:leaderboards:{$leaderboard_type_id}:{$release_id}:{$mode_id}:{$seeded_type_id}:{$multiplayer_type_id}:{$soundtrack_id}:entries:{$date->format('Y-m-d')}";
        
        return LeaderboardEntriesResource::collection(
            Cache::store('opcache')->remember($cache_key, 5, function() use(
                $player_id,
                $date,
                $leaderboard_source,
                $leaderboard_type_id,
                $release_id, 
                $mode_id,
                $seeded_type_id,
                $multiplayer_type_id,
                $soundtrack_id
            ) {
                return LeaderboardEntries::getPlayerCategoryApiReadQuery(
                    $player_id,
                    $date,
                    $leaderboard_source,
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
    public function playerDailyIndex(ReadPlayerDailyLeaderboardEntries $request) {
        $leaderboard_source = LeaderboardSources::getByName($request->leaderboard_source);
    
        $player_id = $request->player_id;
        $character_id = Characters::getByName($request->character)->id;
        $release_id = Releases::getByName($request->release)->id;
        $mode_id = Modes::getByName($request->mode)->id;
        $multiplayer_type_id = MultiplayerTypes::getByName($request->multiplayer_type)->id;
        
        
        /* ---------- Data Provider ---------- */
        
        $data_provider = new SqlDataProvider(LeaderboardEntries::getPlayerDailyApiReadQuery(
            $player_id, 
            $leaderboard_source, 
            $character_id,
            $release_id, 
            $mode_id,
            $multiplayer_type_id
        ));
        
        
        /* ---------- Dataset ---------- */
        
        $cache_key = "players:{$leaderboard_source->name}:{$player_id}:leaderboards:{$release_id}:{$mode_id}:daily:{$character_id}:{$multiplayer_type_id}:entries";
        
        $dataset = new Dataset($cache_key, $data_provider);
        
        $dataset->setFromRequest($request);
        
        $dataset->process();
        
        return LeaderboardEntriesResource::collection($dataset->getPaginator());
    }
}
