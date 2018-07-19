<?php

namespace App;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Components\PostgresCursor;
use App\Traits\HasTempTable;
use App\Traits\HasManualSequence;
use App\ExternalSites;

class SteamUsers extends Model {
    use HasTempTable, HasManualSequence;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'steam_users';
    
    /**
     * The primary key associated with the model.
     *
     * @var string
     */
    protected $primaryKey = 'steam_user_id';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    
    public static function createTemporaryTable() {    
        DB::statement("
            CREATE TEMPORARY TABLE " . static::getTempTableName() . "
            (
                steam_user_id integer,
                steamid bigint,
                communityvisibilitystate smallint,
                profilestate smallint,
                personaname character varying(255),
                profileurl text,
                avatar text,
                avatarmedium text,
                avatarfull text,
                updated timestamp without time zone
            )
            ON COMMIT DROP;
        ");
    }
    
    public static function saveNewTemp() {
        DB::statement("
            INSERT INTO steam_users (
                steam_user_id, 
                steamid, 
                updated
            )
            SELECT 
                steam_user_id,
                steamid,
                updated
            FROM " . static::getTempTableName() . "
        ");
    }
    
    public static function updateFromTemp() {    
        DB::update("
            UPDATE steam_users su
            SET 
                communityvisibilitystate = sut.communityvisibilitystate,
                profilestate = sut.profilestate,
                personaname = sut.personaname,
                profileurl = sut.profileurl,
                avatar = sut.avatar,
                avatarmedium = sut.avatarmedium,
                avatarfull = sut.avatarfull,
                updated = sut.updated
            FROM " . static::getTempTableName() . " sut
            WHERE su.steam_user_id = sut.steam_user_id
        ");
    }
    
    public static function getAllIdsBySteamid() {
        $query = static::select([
            'steam_user_id',
            'steamid'
        ]);
        
        $cursor = new PostgresCursor(
            'steam_user_ids', 
            $query,
            20000
        );
        
        $ids_by_steamid = [];
        
        foreach($cursor->getRecord() as $steam_user) {
            $ids_by_steamid[$steam_user->steamid] = $steam_user->steam_user_id;
        }
        
        return $ids_by_steamid;
    }
    
    public static function getOutdatedIdsQuery() {        
        $thirty_days_ago = new DateTime('-30 day');
        
        return static::select([
            'steam_user_id',
            'steamid'
        ])->where('updated', '<', $thirty_days_ago->format('Y-m-d H:i:s'));
    }
    
    public static function getCacheQuery() {        
        $query = DB::table('steam_users AS su')
            ->select([
                'su.steam_user_id',
                'su.personaname'
            ])
            ->orderBy('su.personaname', 'asc');
        
        ExternalSites::addSiteIdSelectFields($query);
        
        return $query;
    }
    
    public static function getApiReadQuery(array $steamids = []) {
        $query = DB::table('steam_users AS su')
            ->select([
                'su.steam_user_id',
                'su.steamid',
                'su.personaname',
                'su.profileurl',
            ])
            ->orderBy('su.personaname', 'asc');
        
        if(!empty($steamids)) {
            $any_values = '{' . implode(',', $steamids) . '}';
            
            // Add the ANY criteria to the dataset query           
            $query->whereRaw("su.steamid = ANY(?::bigint[])", [
                $any_values
            ]);
        }
        
        return $query;
    }
}
