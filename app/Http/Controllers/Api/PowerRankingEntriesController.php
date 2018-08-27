<?php

namespace App\Http\Controllers\Api;

use DateTime;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PowerRankingEntriesResource;
use App\Http\Requests\Api\ReadPowerRankingEntries;
use App\Http\Requests\Api\ReadPowerRankingCharacterEntries;
use App\Http\Requests\Api\ReadSteamUserPowerRankingEntries;
use App\Http\Requests\Api\ReadSteamUserCharacterRankingEntries;
use App\Components\CacheNames\Rankings\Power as CacheNames;
use App\Components\Dataset\Dataset;
use App\Components\Dataset\Indexes\Sql as SqlIndex;
use App\Components\Dataset\DataProviders\Sql as SqlDataProvider;
use App\PowerRankingEntries;
use App\Releases;
use App\Modes;
use App\Characters;

class PowerRankingEntriesController extends Controller {
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api')->except([
            'index',
            'scoreIndex',
            'speedIndex',
            'deathlessIndex',
            'characterIndex',
            'playerIndex',
            'playerScoreIndex',
            'playerSpeedIndex',
            'playerDeathlessIndex',
            'playerCharacterIndex',
        ]);
    }
    
    /**
     * Retrieves a paginated listing of the current entries request.
     *
     * @param string $index_name
     * @param \Illuminate\Http\Request  $request
     * @param \Illuminate\Database\Query\Builder $query
     * @param callable $sort_callback
     * @return \Illuminate\Http\Response
     */
    protected function getEntriesResponse(string $index_name, Request $request, Builder $query, callable $sort_callback) {
        /* ---------- Data Provider ---------- */
        
        $data_provider = new SqlDataProvider($query);
        
        
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
        
        $dataset->setSortCallback($sort_callback);
        
        $dataset->process();
        
        return PowerRankingEntriesResource::collection($dataset->getPaginator());
    }

    /**
     * Display a listing of power ranking entries.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(ReadPowerRankingEntries $request) {
        $release_id = Releases::getByName($request->release)->release_id;
        $mode_id = Modes::getByName($request->mode)->mode_id;
        $date = new DateTime($request->date);
    
        return static::getEntriesResponse(
            CacheNames::getBase($release_id, $mode_id, $request->seeded),
            $request,
            PowerRankingEntries::getApiReadQuery($release_id, $mode_id, $request->seeded, $date),
            function($entry, $key) {
                return $entry->rank;
            }
        );
    }
    
    /**
     * Display a listing of score ranking entries.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function scoreIndex(ReadPowerRankingEntries $request) {
        $release_id = Releases::getByName($request->release)->release_id;
        $mode_id = Modes::getByName($request->mode)->mode_id;
        $date = new DateTime($request->date);
    
        return static::getEntriesResponse(
            CacheNames::getScore($release_id, $mode_id, $request->seeded),
            $request,
            PowerRankingEntries::getApiReadQuery($release_id, $mode_id, $request->seeded, $date),
            function($entry, $key) {
                return $entry->score_rank;
            }
        );
    }
    
    /**
     * Display a listing of speed ranking entries.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function speedIndex(ReadPowerRankingEntries $request) {
        $release_id = Releases::getByName($request->release)->release_id;
        $mode_id = Modes::getByName($request->mode)->mode_id;
        $date = new DateTime($request->date);
    
        return static::getEntriesResponse(
            CacheNames::getSpeed($release_id, $mode_id, $request->seeded),
            $request,
            PowerRankingEntries::getApiReadQuery($release_id, $mode_id, $request->seeded, $date),
            function($entry, $key) {
                return $entry->speed_rank;
            }
        );
    }
    
    /**
     * Display a listing of deathless ranking entries.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function deathlessIndex(ReadPowerRankingEntries $request) {
        $release_id = Releases::getByName($request->release)->release_id;
        $mode_id = Modes::getByName($request->mode)->mode_id;
        $date = new DateTime($request->date);
    
        return static::getEntriesResponse(
            CacheNames::getDeathless($release_id, $mode_id, $request->seeded),
            $request,
            PowerRankingEntries::getApiReadQuery($release_id, $mode_id, $request->seeded, $date),
            function($entry, $key) {
                return $entry->deathless_rank;
            }
        );
    }
    
    /**
     * Display a listing of character ranking entries.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function characterIndex(ReadPowerRankingCharacterEntries $request) {
        $character_name = $request->character;
    
        $release_id = Releases::getByName($request->release)->release_id;
        $mode_id = Modes::getByName($request->mode)->mode_id;
        $character_id = Characters::getByName($character_name)->character_id;
        $date = new DateTime($request->date);
        
        return static::getEntriesResponse(
            CacheNames::getCharacter($release_id, $mode_id, $request->seeded, $character_id),
            $request,
            PowerRankingEntries::getApiReadQuery($release_id, $mode_id, $request->seeded, $date),
            function($entry, $key) use ($character_name) {
                return $entry->characters[$character_name]['rank'];
            }
        );
    }
    
    /**
     * Display a listing of power ranking entries for the specified player.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function playerIndex($steamid, ReadSteamUserPowerRankingEntries $request) {
        $release_id = Releases::getByName($request->release)->release_id;
        $mode_id = Modes::getByName($request->mode)->mode_id;
    
        /* ---------- Data Provider ---------- */
        
        $data_provider = new SqlDataProvider(
            PowerRankingEntries::getSteamUserApiReadQuery(
                $steamid, 
                $release_id, 
                $mode_id, 
                $request->seeded
            )
        );
        
        
        /* ---------- Dataset ---------- */
        
        $cache_key = "players:steam:{$steamid}:rankings:power:{$release_id}:{$mode_id}:{$request->seeded}";
        
        $dataset = new Dataset($cache_key, $data_provider);
        
        $dataset->setFromRequest($request);
        
        $dataset->setBinaryFields([
            'characters'
        ]);
        
        $dataset->process();
        
        return PowerRankingEntriesResource::collection($dataset->getPaginator());
    }
    
    /**
     * Display a listing of score ranking entries for the specified player.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function playerScoreIndex($steamid, ReadSteamUserPowerRankingEntries $request) {
        $release_id = Releases::getByName($request->release)->release_id;
        $mode_id = Modes::getByName($request->mode)->mode_id;
    
        /* ---------- Data Provider ---------- */
        
        $data_provider = new SqlDataProvider(
            PowerRankingEntries::getSteamUserScoreApiReadQuery(
                $steamid, 
                $release_id, 
                $mode_id, 
                $request->seeded
            )
        );
        
        
        /* ---------- Dataset ---------- */
        
        $cache_key = "players:steam:{$steamid}:rankings:score:{$release_id}:{$mode_id}:{$request->seeded}";
        
        $dataset = new Dataset($cache_key, $data_provider);
        
        $dataset->setFromRequest($request);
        
        $dataset->setBinaryFields([
            'characters'
        ]);
        
        $dataset->process();
        
        return PowerRankingEntriesResource::collection($dataset->getPaginator());
    }
}
