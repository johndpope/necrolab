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
use App\Http\Requests\Api\ReadSteamUserPowerRankingEntries;
use App\Http\Requests\Api\ReadSteamUserCategoryRankingEntries;
use App\Http\Requests\Api\ReadSteamUserCharacterRankingEntries;
use App\Components\CacheNames\Rankings\Power as CacheNames;
use App\Components\Dataset\Dataset;
use App\Components\Dataset\Indexes\Sql as SqlIndex;
use App\Components\Dataset\DataProviders\Sql as SqlDataProvider;
use App\PowerRankingEntries;
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
        $seeded_type_id = SeededTypes::getByName($request->seeded_type)->id;
        $date = new DateTime($request->date);
    
        return $this->getEntriesResponse(
            CacheNames::getBase($release_id, $mode_id, $seeded_type_id),
            $request,
            PowerRankingEntries::getApiReadQuery($release_id, $mode_id, $seeded_type_id, $date),
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
        $leaderboard_type = LeaderboardTypes::getByName($request->leaderboard_type);
        
        $leaderboard_type_id = $leaderboard_type->leaderboard_type_id;
        $release_id = Releases::getByName($request->release)->release_id;
        $mode_id = Modes::getByName($request->mode)->mode_id;
        $seeded_type_id = SeededTypes::getByName($request->seeded_type)->id;
        $date = new DateTime($request->date);
    
        return $this->getEntriesResponse(
            CacheNames::getCategory($leaderboard_type_id, $release_id, $mode_id, $seeded_type_id),
            $request,
            PowerRankingEntries::getApiReadQuery($release_id, $mode_id, $seeded_type_id, $date),
            function($entry, $key) use ($leaderboard_type) {
                $rank_name = "{$leaderboard_type->name}_rank";
                
                if(!isset($entry->$rank_name)) {
                    throw new Exception("Leaderboard type '{$leaderboard_type->name}' is not supported in power rankings.");
                }
            
                return $entry->$rank_name;
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
        $seeded_type_id = SeededTypes::getByName($request->seeded_type)->id;
        $date = new DateTime($request->date);
        
        return $this->getEntriesResponse(
            CacheNames::getCharacter($release_id, $mode_id, $seeded_type_id, $character_id),
            $request,
            PowerRankingEntries::getApiReadQuery($release_id, $mode_id, $seeded_type_id, $date),
            function($entry, $key) use ($character_name) {
                return $entry->characters[$character_name]['rank'];
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
    protected function getPlayerEntriesResponse(string $index_name, Request $request, Builder $query, callable $filter_callback = NULL) {
        /* ---------- Data Provider ---------- */
        
        $data_provider = new SqlDataProvider($query);
        
        
        /* ---------- Dataset ---------- */
        
        $dataset = new Dataset($index_name, $data_provider);
        
        $dataset->setFromRequest($request);
        
        $dataset->setBinaryFields([
            'characters'
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
    public function playerIndex($steamid, ReadSteamUserPowerRankingEntries $request) {
        $release_id = Releases::getByName($request->release)->release_id;
        $mode_id = Modes::getByName($request->mode)->mode_id;
        $seeded_type_id = SeededTypes::getByName($request->seeded_type)->id;
        
        return $this->getPlayerEntriesResponse(
            CacheNames::getPlayer($steamid, $release_id, $mode_id, $seeded_type_id),
            $request,
            PowerRankingEntries::getSteamUserApiReadQuery(
                $steamid, 
                $release_id, 
                $mode_id, 
                $seeded_type_id
            )
        );
    }
    
    /**
     * Display a listing of score ranking entries for the specified player.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function playerCategoryIndex($steamid, ReadSteamUserCategoryRankingEntries $request) {
        $leaderboard_type = LeaderboardTypes::getByName($request->leaderboard_type);
    
        $leaderboard_type_id = $leaderboard_type->leaderboard_type_id;
        $release_id = Releases::getByName($request->release)->release_id;
        $mode_id = Modes::getByName($request->mode)->mode_id;
        $seeded_type_id = SeededTypes::getByName($request->seeded_type)->id;
        
        return $this->getPlayerEntriesResponse(
            CacheNames::getPlayerCategory($steamid, $leaderboard_type_id, $release_id, $mode_id, $seeded_type_id),
            $request,
            PowerRankingEntries::getSteamUserCategoryApiReadQuery(
                $steamid,
                $leaderboard_type,
                $release_id, 
                $mode_id, 
                $seeded_type_id
            )
        );
    }
    
    /**
     * Display a listing of character ranking entries for the specified player.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function playerCharacterIndex($steamid, ReadSteamUserCharacterRankingEntries $request) {
        $release_id = Releases::getByName($request->release)->release_id;
        $mode_id = Modes::getByName($request->mode)->mode_id;
        $character_name = $request->character;
        $character_id = Characters::getByName($character_name)->character_id;
        $seeded_type_id = SeededTypes::getByName($request->seeded_type)->id;

        return $this->getPlayerEntriesResponse(
            CacheNames::getPlayerCharacter($steamid, $release_id, $mode_id, $seeded_type_id, $character_id),
            $request,
            PowerRankingEntries::getSteamUserApiReadQuery(
                $steamid, 
                $release_id, 
                $mode_id, 
                $seeded_type_id
            ),
            /*
                Since character data is now stored in a bytea field all power ranking entries for this
                player need to be retrieved, and the data checked for if the specified character rank exists.
                This is expensive so other solutions will be looked into as soon as possible.
            */
            function($entry) use ($character_name) {
                return isset($entry->characters[$character_name]);
            }
        );
    }
}
