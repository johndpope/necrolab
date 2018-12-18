<?php

namespace App;

use DateTime;
use DateInterval;
use stdClass;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use App\Traits\HasTempTable;
use App\Traits\HasManualSequence;
use App\Traits\AddsSqlCriteria;
use App\Components\PostgresCursor;
use App\DateIncrementor;
use App\CallbackHandler;
use App\Characters;
use App\Releases;
use App\Modes;
use App\LeaderboardTypes;
use App\SeededTypes;
use App\Soundtracks;
use App\MultiplayerTypes;

class Leaderboards extends Model {
    use HasTempTable, HasManualSequence, AddsSqlCriteria;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'leaderboards';
    
    /**
     * The primary key associated with the model.
     *
     * @var string
     */
    protected $primaryKey = 'leaderboard_id';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    
    public static function getSeededFlags() {
        return [
            0, 
            1
        ];
    }
    
    public static function generateUrlName(object $leaderboard) {    
        $url_name_segments = [];
        
        $leaderboard_type_name = NULL;
        
        if(!empty($leaderboard->leaderboard_type)) {
            $url_name_segments[2] = $leaderboard->leaderboard_type->name;
            
            $leaderboard_type_name = $leaderboard->leaderboard_type->name;
        }
        
        if(
            !empty($leaderboard->character) &&
            (
                $leaderboard_type_name == 'daily' && $leaderboard->character != 'cadence' ||
                $leaderboard_type_name != 'daily'
            )
        ) {
            $url_name_segments[1] = $leaderboard->character->name;
        }
        
        if(!empty($leaderboard->release)) {
            $url_name_segments[3] = $leaderboard->release->name;
        }
        
        // Since dailies aren't tied to a mode or the flags skip this part for that type
        if(
            $leaderboard_type_name != 'daily' &&
            $leaderboard_type_name != 'deathless'
        ) {        
            if(!empty($leaderboard->mode) && $leaderboard->mode->name != 'normal') {
                $url_name_segments[4] = $leaderboard->mode->name;
            }
            
            if($leaderboard->seeded_type->name != 'unseeded') {
                $url_name_segments[5] = $leaderboard->seeded_type->name;
            }
        }
        
        if($leaderboard->multiplayer_type->name != 'single') {
            $url_name_segments[6] = $leaderboard->multiplayer_type->name;
        }
        
        if($leaderboard->soundtrack->name != 'default') {
            $url_name_segments[7] = $leaderboard->soundtrack->name;
        }
        
        ksort($url_name_segments);
        
        $url_name = implode('-', $url_name_segments);
        
        return str_replace('_', '-', $url_name);
    }
    
    public static function setPropertiesFromName(object $leaderboard) {
        if(!isset($leaderboard->name)) {
            throw new Exception('name property in specified leaderboard object is required but not found.');
        }

        $leaderboard->release = Releases::getReleaseFromString($leaderboard->name);
        
        $leaderboard->mode = Modes::getModeFromString($leaderboard->name);
        
        $leaderboard->character = Characters::getRecordFromMatch($leaderboard->name, Characters::getAllByName());
        
        $leaderboard->leaderboard_type = LeaderboardTypes::getTypeFromString($leaderboard->name);
        
        $leaderboard->seeded_type = SeededTypes::getFromString($leaderboard->name);
        
        $leaderboard->soundtrack = Soundtracks::getFromString($leaderboard->name);
        
        $leaderboard->multiplayer_type = MultiplayerTypes::getFromString($leaderboard->name);
        
        /*
            If this run is a daily then grab the date it is for.
            Date matching solution found at: http://stackoverflow.com/a/7645146
            Date filtering solution found at: http://stackoverflow.com/a/4639488  
        */
        $unformatted_daily_date = preg_replace("/[^0-9\/]/", "", $leaderboard->name);
        $daily_date = NULL;
        
        if(!empty($unformatted_daily_date)) {            
            $leaderboard->leaderboard_type = LeaderboardTypes::getByName('daily');
            
            $daily_date = DateTime::createFromFormat('d/m/Y', $unformatted_daily_date);

            $last_errors = DateTime::getLastErrors();
            
            if(!(empty($last_errors['warning_count']) && empty($last_errors['error_count']))) {
                $daily_date = NULL;
            }
        }
        
        $leaderboard->daily_date = NULL;
                                                        
        if(!empty($daily_date)) {
            $leaderboard->daily_date = $daily_date->format('Y-m-d');  
        } 
        
        $leaderboard->ranking_types = [];

        if(
            $leaderboard->leaderboard_type->name != 'daily' && 
            $leaderboard->soundtrack->name == 'default' &&
            $leaderboard->multiplayer_type->name == 'single'
        ) {
            $leaderboard->ranking_types[] = RankingTypes::getByName('power');
            $leaderboard->ranking_types[] = RankingTypes::getByName('super');
        }
        
        if(
            $leaderboard->leaderboard_type->name == 'daily' && 
            $leaderboard->character->name == 'cadence' &&
            !empty($daily_date) &&
            $leaderboard->soundtrack->name == 'default' && 
            $leaderboard->multiplayer_type->name == 'single' && 
            $leaderboard->seeded_type->name == 'unseeded'
        ) {
            $leaderboard->ranking_types[] = RankingTypes::getByName('daily');
        }
    }
    
    public static function isValid(object $leaderboard, DateTime $date) {
        $daily_date_difference = NULL;
        
        $daily_date = NULL;
        
        if($leaderboard->leaderboard_type->name == 'daily') {
            $daily_date = new DateTime($leaderboard->daily_date);
        
            $daily_date_difference = $date->diff($daily_date);
        }
        
        //$blacklist_record = Blacklist::getRecordById($leaderboard->lbid);
        
        $date_within_release = false;
        
        $start_date = new DateTime($leaderboard->release->start_date);
        $end_date = new DateTime($leaderboard->release->end_date);
        
        if($date >= $start_date && $date <= $end_date) {
            $date_within_release = true;
        }
        
        $is_valid = false;
        
        if(
            //empty($blacklist_record) && 
            !empty($leaderboard->character->character_id) && 
            $date_within_release 
            //($this->is_daily == 0) || ($this->is_daily == 1 && $this->is_daily_ranking == 1 && $daily_date >= $date && $daily_date_difference->format('%a') == 1)
        ) {
            $is_valid = true;
        }
    
        return $is_valid;
    }
    
    public static function getXmlUrls() {
        $start_date = new DateTime('2017-01-01');
        $end_date = new DateTime(date('Y-m-d'));
        
        $current_date = clone $start_date;
        
        $base_s3_url = env('AWS_URL');
        
        $xml_urls = [];
        
        while($current_date <= $end_date) {
            $curent_date_formatted = $current_date->format('Y-m-d');
        
            $xml_url = new stdClass();
            
            $xml_url->date = $curent_date_formatted;
            $xml_url->url = "{$base_s3_url}/leaderboard_xml/{$curent_date_formatted}.zip";
            
            $xml_urls[] = $xml_url;
        
            $current_date->add(new DateInterval('P1D'));
        }
        
        return $xml_urls;
    }
    
    public static function createTemporaryTable() {    
        DB::statement("
            CREATE TEMPORARY TABLE leaderboards_temp (
                leaderboard_id integer,
                lbid character varying(20),
                name character varying(255),
                display_name text,
                url_name character varying(255),
                url text,
                character_id smallint,
                leaderboard_type_id smallint,
                release_id smallint,
                mode_id smallint,
                daily_date date,
                seeded_type_id smallint,
                soundtrack_id smallint,
                multiplayer_type_id smallint
            )
            ON COMMIT DROP;
        ");
    }
    
    public static function saveTemp() {
        DB::statement("
            INSERT INTO leaderboards (
                leaderboard_id,
                lbid,
                name,
                display_name,
                url_name,
                url,
                character_id,
                leaderboard_type_id,
                release_id,
                mode_id,
                daily_date,
                seeded_type_id,
                soundtrack_id,
                multiplayer_type_id
            )
            SELECT 
                leaderboard_id,
                lbid,
                name,
                display_name,
                url_name,
                url,
                character_id,
                leaderboard_type_id,
                release_id,
                mode_id,
                daily_date,
                seeded_type_id,
                soundtrack_id,
                multiplayer_type_id
            FROM " . static::getTempTableName() . "
        ");
    }
    
    public static function updateUrlNamesFromTemp() {
        DB::statement("
            UPDATE leaderboards l
            SET 
                url_name = lt.url_name
            FROM " . static::getTempTableName() . " lt
            WHERE l.leaderboard_id = lt.leaderboard_id
        ");
    }
    
    public static function getIdsByLbid() {
        $query = DB::table('leaderboards')->select([
            'lbid',
            'leaderboard_id'
        ]);
        
        $cursor = new PostgresCursor(
            'leaderboards_by_lbid', 
            $query,
            5000
        );
        
        $ids_by_lbid = [];
        
        foreach($cursor->getRecord() as $leaderboard) {
            $ids_by_lbid[$leaderboard->lbid] = $leaderboard->leaderboard_id;
        }
        
        return $ids_by_lbid;
    }
    
    public static function getApiReadQuery() {
        return DB::table('leaderboards AS l')
            ->select([
                'l.leaderboard_id',
                'l.lbid',
                'l.name',
                'l.display_name',
                'l.url_name',
                'c.name AS character_name',
                'lt.name AS leaderboard_type_name',
                DB::raw('string_agg(DISTINCT rt.name, \',\') AS rankings'),
                'st.name AS seeded_type',
                'strk.name AS soundtrack',
                'mt.name AS multiplayer_type'
            ])
            ->join('characters AS c', 'c.character_id', '=', 'l.character_id')
            ->join('leaderboard_types AS lt', 'lt.leaderboard_type_id', '=', 'l.leaderboard_type_id')
            ->join('seeded_types AS st', 'st.id', '=', 'l.seeded_type_id')
            ->join('soundtracks AS strk', 'strk.id', '=', 'l.soundtrack_id')
            ->join('multiplayer_types AS mt', 'mt.id', '=', 'l.multiplayer_type_id')
            ->leftJoin('leaderboard_ranking_types AS lrt', 'lrt.leaderboard_id', '=', 'l.leaderboard_id')
            ->leftJoin('ranking_types AS rt', 'rt.id', '=', 'lrt.ranking_type_id')
            ->groupBy(
                'l.leaderboard_id',
                'l.lbid',
                'l.name',
                'l.display_name',
                'c.name',
                'lt.name',
                'st.name',
                'strk.name',
                'mt.name'
            )
            ->orderBy('lt.name', 'asc')
            ->orderBy('l.name', 'asc');
    }
    
    public static function getNonDailyApiReadQuery(int $release_id, int $mode_id, int $character_id) {
        return static::getApiReadQuery()
            ->where('l.release_id', $release_id)
            ->where('l.mode_id', $mode_id)
            ->where('l.character_id', $character_id)
            ->where('lt.name', '!=', 'daily');
    }
    
    public static function getCategoryApiReadQuery(int $leaderboard_type_id, int $release_id, int $mode_id, int $character_id) {
        return static::getApiReadQuery()
            ->where('l.release_id', $release_id)
            ->where('l.mode_id', $mode_id)
            ->where('l.character_id', $character_id)
            ->where('l.leaderboard_type_id', $leaderboard_type_id);
    }
    
    public static function getApiShowQuery(string $lbid) {
        return static::getApiReadQuery()
            ->where('l.lbid', $lbid);
    }
    
    public static function getApiUrlShowQuery(string $url_name) {
        return static::getApiReadQuery()
            ->where('l.url_name', $url_name);
    }
    
    public static function getDailyApiReadQuery(int $release_id, int $mode_id) {    
        return DB::table('leaderboards AS l')
            ->select([
                'l.daily_date',
                'ls.players',
                'ls.score'
            ])
            ->join('leaderboard_types AS lt', 'lt.leaderboard_type_id', '=', 'l.leaderboard_type_id')
            ->join('leaderboard_snapshots AS ls', function($join) {
                $join->on('ls.leaderboard_id', '=', 'l.leaderboard_id')
                    ->on('ls.date', '=', 'l.daily_date');
            })
            ->where('l.release_id', $release_id)
            ->where('l.mode_id', $mode_id)
            ->where('lt.name', 'daily')
            ->orderBy('l.daily_date', 'desc');
    }
    
    public static function getPlayerApiReadQuery(string $steamid) {
        return DB::table('steam_user_pbs AS sup')
            ->select([
                'l.leaderboard_id',
                'l.lbid',
                'l.name',
                'l.display_name',
                'c.name AS character_name',
                'lt.name AS leaderboard_type_name',
                DB::raw('string_agg(DISTINCT rt.name, \',\') AS rankings'),
                'st.name AS seeded_type',
                'strk.name AS soundtrack',
                'mt.name AS multiplayer_type'
            ])
            ->join('steam_users AS su', 'su.steam_user_id', '=', 'sup.steam_user_id')
            ->join('leaderboards AS l', 'l.leaderboard_id', '=', 'sup.leaderboard_id')
            ->join('characters AS c', 'c.character_id', '=', 'l.character_id')
            ->join('leaderboard_types AS lt', 'lt.leaderboard_type_id', '=', 'l.leaderboard_type_id')
            ->join('seeded_types AS st', 'st.id', '=', 'l.seeded_type_id')
            ->join('soundtracks AS strk', 'strk.id', '=', 'l.soundtrack_id')
            ->join('multiplayer_types AS mt', 'mt.id', '=', 'l.multiplayer_type_id')
            ->leftJoin('leaderboard_ranking_types AS lrt', 'lrt.leaderboard_id', '=', 'l.leaderboard_id')
            ->leftJoin('ranking_types AS rt', 'rt.id', '=', 'lrt.ranking_type_id')
            ->where('su.steamid', $steamid)
            ->groupBy(
                'l.leaderboard_id',
                'l.lbid',
                'l.name',
                'l.display_name',
                'c.name',
                'lt.name',
                'st.name',
                'strk.name',
                'mt.name'
            )
            ->orderBy('lt.name', 'asc')
            ->orderBy('l.name', 'asc');
    }
    
    public static function getPlayerNonDailyApiReadQuery(string $steamid, int $release_id, int $mode_id, int $character_id) {
        return static::getPlayerApiReadQuery($steamid)
            ->where('l.release_id', $release_id)
            ->where('l.mode_id', $mode_id)
            ->where('l.character_id', $character_id)
            ->where('lt.name', '!=', 'daily');
    }
    
    public static function getPlayerCategoryApiReadQuery(string $steamid, int $leaderboard_type_id, int $release_id, int $mode_id, int $character_id) {
        return static::getPlayerApiReadQuery($steamid)
            ->where('l.release_id', $release_id)
            ->where('l.mode_id', $mode_id)
            ->where('l.character_id', $character_id)
            ->where('l.leaderboard_type_id', $leaderboard_type_id);
    }
    
    public static function getPlayerDailyApiReadQuery(string $steamid, int $release_id, int $mode_id) {    
        return DB::table('steam_user_pbs AS sup')
            ->select([
                'l.leaderboard_id',
                'l.daily_date'
            ])
            ->join('steam_users AS su', 'su.steam_user_id', '=', 'sup.steam_user_id')
            ->join('leaderboards AS l', 'l.leaderboard_id', '=', 'sup.leaderboard_id')
            ->join('leaderboard_types AS lt', 'lt.leaderboard_type_id', '=', 'l.leaderboard_type_id')
            ->where('l.release_id', $release_id)
            ->where('l.mode_id', $mode_id)
            ->where('lt.name', 'daily')
            ->groupBy(
                'l.leaderboard_id',
                'l.daily_date'
            )
            ->orderBy('l.daily_date', 'desc');
    }
}
