<?php

namespace App;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Traits\HasTempTable;
use App\Traits\HasManualSequence;
use App\Components\PostgresCursor;
use App\Characters;
use App\Releases;
use App\Modes;
use App\LeaderboardTypes;

class Leaderboards extends Model {
    use HasTempTable, HasManualSequence;

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
    
    public static function getSeededFlagFromName($seeded_name) {
        $is_seeded = NULL;
        
        switch($seeded_name) {
            case 'seeded':
                $is_seeded = 1;
                break;
            case 'unseeded':
                $is_seeded = 0;
                break;
        }
        
        return $is_seeded;
    }
    
    public static function getCoOpFlagFromName($co_op_name) {
        $co_op = NULL;
        
        switch($co_op_name) {
            case 'co_op':
                $co_op = 1;
                break;
            case 'single':
                $co_op = 0;
                break;
        }
        
        return $co_op;
    }
    
    public static function getCustomFlagFromName($custom_name) {
        $custom = NULL;
        
        switch($custom_name) {
            case 'custom':
                $custom = 1;
                break;
            case 'default':
                $custom = 0;
                break;
        }
        
        return $custom;
    }
    
    public static function setPropertiesFromName(object $leaderboard) {
        if(!isset($leaderboard->name)) {
            throw new Exception('name property in specified leaderboard object is required but not found.');
        }
    
        $leaderboard->is_custom = 0;
        
        if(stripos($leaderboard->name, 'custom') !== false) {
            $leaderboard->is_custom = 1;
        }
        
        $leaderboard->is_co_op = 0;
        
        if(stripos($leaderboard->name, 'co-op') !== false) {
            $leaderboard->is_co_op = 1;
        }
        
        $leaderboard->is_seeded = 0;
        
        if(stripos($leaderboard->name, 'seeded') !== false) {
            $leaderboard->is_seeded = 1;
        }

        $leaderboard->release = Releases::getReleaseFromString($leaderboard->name);
        
        $leaderboard->mode = Modes::getModeFromString($leaderboard->name);
        
        $leaderboard->character = Characters::getRecordFromMatch($leaderboard->name, Characters::getAllByName());
        
        $leaderboard->leaderboard_type = LeaderboardTypes::getTypeFromString($leaderboard->name);
        
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
            empty($leaderboard->is_custom) && 
            empty($leaderboard->is_co_op)
        ) {
            $leaderboard->ranking_types[] = RankingTypes::getByName('power');
            $leaderboard->ranking_types[] = RankingTypes::getByName('super');
        }
        
        if(
            $leaderboard->leaderboard_type->name == 'daily' && 
            $leaderboard->character->name == 'cadence' &&
            !empty($daily_date) &&
            empty($leaderboard->is_custom) && 
            empty($leaderboard->is_co_op) && 
            empty($leaderboard->is_seeded)
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
    
    public static function createTemporaryTable() {    
        DB::statement("
            CREATE TEMPORARY TABLE leaderboards_temp (
                leaderboard_id integer,
                lbid character varying(20),
                name character varying(255),
                display_name text,
                url text,
                character_id smallint,
                leaderboard_type_id smallint,
                release_id smallint,
                mode_id smallint,
                daily_date date,
                is_custom smallint,
                is_co_op smallint,
                is_seeded smallint
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
                url,
                character_id,
                leaderboard_type_id,
                release_id,
                mode_id,
                daily_date,
                is_custom,
                is_co_op,
                is_seeded
            )
            SELECT 
                leaderboard_id,
                lbid,
                name,
                display_name,
                url,
                character_id,
                leaderboard_type_id,
                release_id,
                mode_id,
                daily_date,
                is_custom,
                is_co_op,
                is_seeded
            FROM leaderboards_temp
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
                'c.name AS character_name',
                'lt.name AS leaderboard_type_name',
                DB::raw('string_agg(rt.name, \',\') AS rankings'),
                'l.is_custom',
                'l.is_co_op',
                'l.is_seeded'
            ])
            ->join('characters AS c', 'c.character_id', '=', 'l.character_id')
            ->join('leaderboard_types AS lt', 'lt.leaderboard_type_id', '=', 'l.leaderboard_type_id')
            ->leftJoin('leaderboard_ranking_types AS lrt', 'lrt.leaderboard_id', '=', 'l.leaderboard_id')
            ->leftJoin('ranking_types AS rt', 'rt.id', '=', 'lrt.ranking_type_id')
            ->groupBy(
                'l.leaderboard_id',
                'l.lbid',
                'l.name',
                'l.display_name',
                'c.name',
                'lt.name',
                'l.is_custom',
                'l.is_co_op',
                'l.is_seeded'
            )
            ->orderBy('lt.name', 'asc')
            ->orderBy('l.name', 'asc');
    }
    
    public static function getNonDailyApiReadQuery(int $release_id, int $mode_id) {
        return static::getApiReadQuery()
            ->where('release_id', $release_id)
            ->where('mode_id', $mode_id)
            ->where('lt.name', '!=', 'daily');
    }
    
    public static function getScoreApiReadQuery(int $release_id, int $mode_id) {
        return static::getApiReadQuery()
            ->where('release_id', $release_id)
            ->where('mode_id', $mode_id)
            ->where('lt.name', '=', 'score');
    }
    
    public static function getSpeedApiReadQuery(int $release_id, int $mode_id) {
        return static::getApiReadQuery()
            ->where('release_id', $release_id)
            ->where('mode_id', $mode_id)
            ->where('lt.name', '=', 'speed');
    }
    
    public static function getDeathlessApiReadQuery(int $release_id) {
        $mode_id = Modes::getByName('normal')->mode_id;
    
        return static::getApiReadQuery()
            ->where('release_id', $release_id)
            ->where('mode_id', $mode_id)
            ->where('lt.name', '=', 'deathless');
    }
    
    public static function getApiShowQuery(string $lbid) {
        return static::getApiReadQuery()
            ->where('l.lbid', $lbid);
    }
}