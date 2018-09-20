<?php

namespace App;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Query\Builder;
use App\Components\PostgresCursor;
use App\Traits\HasTempTable;
use App\Traits\HasManualSequence;
use App\Traits\AddsSqlCriteria;
use App\ExternalSites;

class SteamUsers extends Model {
    use HasTempTable, HasManualSequence, AddsSqlCriteria;

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
     * The name of the "created at" column. Disabled in this class.
     *
     * @var string
     */
    const CREATED_AT = NULL;
    
    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    const UPDATED_AT = 'updated';
    
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
                personaname_search_index = to_tsvector(sut.personaname),
                profileurl = sut.profileurl,
                avatar = sut.avatar,
                avatarmedium = sut.avatarmedium,
                avatarfull = sut.avatarfull,
                updated = sut.updated
            FROM " . static::getTempTableName() . " sut
            WHERE su.steam_user_id = sut.steam_user_id
        ");
    }
    
    public static function updateRecordSearchIndex(\App\SteamUsers $record) {    
        DB::update("
            UPDATE steam_users
            SET personaname_search_index = to_tsvector(:personaname)
            WHERE steam_user_id = :steam_user_id
        ", [
            ':personaname' => $record->personaname,
            ':steam_user_id' => $record->steam_user_id
        ]);
    }
    
    public static function addSelects(Builder $query) {
        $query->addSelect([
            'su.steamid',
            'su.personaname AS steam_username',
            'su.profileurl AS steam_profile_url',
            'mu.external_id AS mixer_id',
            'mu.username AS mixer_username',
            'du.discord_id',
            'du.username AS discord_username',
            'du.discriminator AS discord_discriminator',
            'ru.reddit_id',
            'ru.username AS reddit_username',
            'tu.twitch_id',
            'tu.user_display_name AS twitch_username',
            'twu.twitter_id',
            'twu.nickname AS twitter_nickname',
            'twu.name AS twitter_name',
            'yu.youtube_id',
            'yu.youtube_id AS youtube_username'
        ]);
    }
    
    public static function addLeftJoins(Builder $query) {    
        $query->leftJoin('users AS u', 'u.steam_user_id', '=', 'su.steam_user_id');
        $query->leftJoin('mixer_users AS mu', 'mu.id', '=', 'u.mixer_user_id');
        $query->leftJoin('discord_users AS du', 'du.discord_user_id', '=', 'u.discord_user_id');
        $query->leftJoin('reddit_users AS ru', 'ru.reddit_user_id', '=', 'u.reddit_user_id');
        $query->leftJoin('twitch_users AS tu', 'tu.twitch_user_id', '=', 'u.twitch_user_id');
        $query->leftJoin('twitter_users AS twu', 'twu.twitter_user_id', '=', 'u.twitter_user_id');
        $query->leftJoin('youtube_users AS yu', 'yu.youtube_user_id', '=', 'u.youtube_user_id');
    }
    
    public static function getAllIdsBySteamid() {
        $query = DB::table('steam_users AS su')
            ->select([
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
    
    public static function getIdsBySearchTerm(string $search_term) {
        $term_hash_name = sha1($search_term);
    
        return Cache::store('opcache')->remember("steam_users:search:{$term_hash_name}", 5, function() use($search_term) {                            
            return static::select([
                'steam_user_id'
            ])
            ->whereRaw('personaname_search_index @@ to_tsquery(?)', [
                $search_term
            ])
            ->pluck('steam_user_id', 'steam_user_id');
        });
    }
    
    public static function getCacheQuery() {        
        $query = DB::table('steam_users AS su')
            ->select([
                'su.steam_user_id'
            ])
            ->leftJoin('users AS u', 'u.steam_user_id', '=', 'su.steam_user_id')
            ->orderBy('su.personaname', 'asc');
        
        ExternalSites::addSiteIdSelectFields($query);
        
        return $query;
    }
    
    public static function getApiReadQuery() {
        $query = DB::table('steam_users AS su');
        
        static::addSelects($query);
        static::addLeftJoins($query);
        
        $query->orderBy('su.personaname', 'asc');
        
        return $query;
    }
}
