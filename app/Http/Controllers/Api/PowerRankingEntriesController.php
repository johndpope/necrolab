<?php

namespace App\Http\Controllers\Api;

use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Database\Query\Builder;
use App\Http\Controllers\Controller;
use App\Http\Resources\PowerRankingEntriesResource;
use App\Http\Requests\Api\ReadPowerRankingEntries;
use App\Http\Requests\Api\ReadCategoryRankingEntries;
use App\Http\Requests\Api\ReadPowerRankingCharacterEntries;
use App\Http\Requests\Api\ReadPlayerPowerRankingEntries;
use App\Http\Requests\Api\ReadPlayerCategoryRankingEntries;
use App\Http\Requests\Api\ReadPlayerCharacterRankingEntries;
use App\Components\RequestModels;
use App\Components\CacheNames\Rankings\Power as CacheNames;
use App\Components\Dataset\Dataset;
use App\Components\Dataset\Indexes\Sql as SqlIndex;
use App\Components\Dataset\DataProviders\Sql as SqlDataProvider;
use App\PowerRankingEntries;
use App\LeaderboardSources;
use App\LeaderboardTypes;
use App\Releases;
use App\Modes;
use App\Characters;
use App\SeededTypes;

class PowerRankingEntriesController extends Controller {
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api')->except([
            'index',
            'categoryIndex',
            'characterIndex',
            'playerIndex',
            'playerCategoryIndex',
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
    protected function getEntriesResponse(
        string $index_name, 
        Request $request, 
        RequestModels $request_models, 
        callable $sort_callback
    ) {
        /* ---------- Data Provider ---------- */
        
        $query = PowerRankingEntries::getApiReadQuery(
            $request_models->leaderboard_source,
            $request_models->release->id,
            $request_models->mode->id,
            $request_models->seeded_type->id,
            $request_models->multiplayer_type->id,
            $request_models->soundtrack->id,
            $request_models->date
        );
        
        $data_provider = new SqlDataProvider($query);
        
        
        /* ---------- Index ---------- */
        
        $index = new SqlIndex($request_models->leaderboard_source, $index_name);
        
        
        /* ---------- Dataset ---------- */
        
        $dataset = new Dataset($request_models->leaderboard_source, $index_name, $data_provider);
        
        $dataset->setIndex($index, 'pre.player_id');
        
        $dataset->setIndexSubName($request_models->date->name);
        
        $dataset->setFromRequest($request);
        
        $dataset->setBinaryFields([
            'characters',
            'category_ranks'
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
        $request_models = new RequestModels($request, [
            'leaderboard_source',
            'release',
            'mode',
            'seeded_type',
            'multiplayer_type',
            'soundtrack',
            'date'
        ]);
        
        $cache_prefix_name = $request_models->getCacheNamePrefix();
        
        unset($cache_prefix_name->leaderboard_source);
        unset($cache_prefix_name->date);
    
        return $this->getEntriesResponse(
            CacheNames::getBase($cache_prefix_name),
            $request,
            $request_models,
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
    public function categoryIndex(ReadCategoryRankingEntries $request) {
        $request_models = new RequestModels($request, [
            'leaderboard_source',
            'leaderboard_type',
            'release',
            'mode',
            'seeded_type',
            'multiplayer_type',
            'soundtrack',
            'date'
        ]);
        
        $cache_prefix_name = $request_models->getCacheNamePrefix();
        
        unset($cache_prefix_name->leaderboard_source);
        unset($cache_prefix_name->leaderboard_type);
        unset($cache_prefix_name->date);
    
        return $this->getEntriesResponse(
            CacheNames::getCategory($cache_prefix_name, $request_models->leaderboard_type->id),
            $request,
            $request_models,
            function($entry, $key) use ($request_models) {                
                if(!isset($entry->category_ranks[$request_models->leaderboard_type->name])) {
                    throw new Exception("Leaderboard type '{$request_models->leaderboard_type->name}' is not supported in power rankings.");
                }
            
                return $entry->category_ranks[$request_models->leaderboard_type->name];
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
        $request_models = new RequestModels($request, [
            'leaderboard_source',
            'character',
            'release',
            'mode',
            'seeded_type',
            'multiplayer_type',
            'soundtrack',
            'date'
        ]);
        
        $cache_prefix_name = $request_models->getCacheNamePrefix();
        
        unset($cache_prefix_name->leaderboard_source);
        unset($cache_prefix_name->character);
        unset($cache_prefix_name->date);
        
        return $this->getEntriesResponse(
            CacheNames::getCharacter($cache_prefix_name, $request_models->character->id),
            $request,
            $request_models,
            function($entry, $key) use ($request_models) {
                return $entry->characters[$request_models->character->name]['rank'];
            }
        );
    }
    
    /**
     * Retrieves a paginated listing of the current entries request for the specified player.
     *
     * @param string $index_name
     * @param \Illuminate\Http\Request  $request
     * @param \Illuminate\Database\Query\Builder $query
     * @param callable $filter_callback (optional)
     * @return \Illuminate\Http\Response
     */
    protected function getPlayerEntriesResponse(
        string $index_name, 
        Request $request, 
        RequestModels $request_models, 
        callable $filter_callback = NULL
    ) {
        /* ---------- Data Provider ---------- */

        $query = PowerRankingEntries::getPlayerApiReadQuery(
            $request->player_id, 
            $request_models->leaderboard_source,
            $request_models->release->id, 
            $request_models->mode->id, 
            $request_models->seeded_type->id,
            $request_models->multiplayer_type->id,
            $request_models->soundtrack->id
        );
        
        $data_provider = new SqlDataProvider($query);
        
        
        /* ---------- Dataset ---------- */
        
        $dataset = new Dataset($request_models->leaderboard_source, $index_name, $data_provider);
        
        $dataset->setFromRequest($request);
        
        $dataset->setBinaryFields([
            'characters',
            'category_ranks'
        ]);
        
        if(isset($filter_callback)) {
            $dataset->setFilterCallback($filter_callback);
        }
        
        $dataset->setSortCallback(function($entry, $key) {
            return 0 - (new DateTime($entry->date))->getTimestamp();
        });
        
        $dataset->process();
        
        return PowerRankingEntriesResource::collection($dataset->getPaginator());
    }
    
    /**
     * Display a listing of power ranking entries for the specified player.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function playerIndex(ReadPlayerPowerRankingEntries $request) {
        $request_models = new RequestModels($request, [
            'leaderboard_source',
            'release',
            'mode',
            'seeded_type',
            'multiplayer_type',
            'soundtrack'
        ]);
        
        $cache_prefix_name = $request_models->getCacheNamePrefix();
        
        unset($cache_prefix_name->leaderboard_source);
        
        return $this->getPlayerEntriesResponse(
            CacheNames::getPlayer($request->player_id, $cache_prefix_name),
            $request,
            $request_models
        );
    }
    
    /**
     * Display a listing of score ranking entries for the specified player.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function playerCategoryIndex(ReadPlayerCategoryRankingEntries $request) {
        $request_models = new RequestModels($request, [
            'leaderboard_source',
            'leaderboard_type',
            'release',
            'mode',
            'seeded_type',
            'multiplayer_type',
            'soundtrack'
        ]);
        
        $cache_prefix_name = $request_models->getCacheNamePrefix();
        
        unset($cache_prefix_name->leaderboard_source);
        unset($cache_prefix_name->leaderboard_type);
        
        return $this->getPlayerEntriesResponse(
            CacheNames::getPlayerCategory($cache_prefix_name, $request->player_id, $request_models->leaderboard_type->id),
            $request,
            $request_models,
            function($entry) use ($request_models) {
                return isset($entry->category_ranks[$request_models->leaderboard_type->name]);
            }
        );
    }
    
    /**
     * Display a listing of character ranking entries for the specified player.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function playerCharacterIndex(ReadPlayerCharacterRankingEntries $request) {
        $request_models = new RequestModels($request, [
            'leaderboard_source',
            'character',
            'release',
            'mode',
            'seeded_type',
            'multiplayer_type',
            'soundtrack',
        ]);
        
        $cache_prefix_name = $request_models->getCacheNamePrefix();
        
        unset($cache_prefix_name->leaderboard_source);
        unset($cache_prefix_name->character);

        return $this->getPlayerEntriesResponse(
            CacheNames::getPlayerCharacter($cache_prefix_name, $request->player_id, $request_models->character->id),
            $request,
            $request_models,
            function($entry) use ($request_models) {
                return isset($entry->characters[$request_models->character->name]);
            }
        );
    }
}
