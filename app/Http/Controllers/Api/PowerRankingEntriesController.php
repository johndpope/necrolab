<?php

namespace App\Http\Controllers\Api;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Database\Query\Builder;
use App\Http\Controllers\Controller;
use App\Http\Resources\PowerRankingEntriesResource;
use App\Http\Requests\Api\ReadPowerRankingEntries;
use App\Http\Requests\Api\ReadPowerRankingCharacterEntries;
use App\Components\CacheNames\Rankings\Power as CacheNames;
use App\Components\EntriesDataset;
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
            'characterIndex'
        ]);
    }
    
    /**
     * Retrieves a paginated listing of the current entries request.
     *
     * @param string $index_name
     * @param  \Illuminate\Http\Request  $request
     * @param \Illuminate\Database\Query\Builder $query
     * @param callable $sort_callback
     * @return \Illuminate\Http\Response
     */
    protected function getEntriesResponse(string $index_name, Request $request, Builder $query, callable $sort_callback) {
        $dataset = new EntriesDataset($index_name, 'su.steam_user_id', $query);
        
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
}
