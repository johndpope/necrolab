<?php

namespace App;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Components\RecordQueue;
use App\Components\InsertQueue;

class SteamUsers extends Model {
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
            CREATE TEMPORARY TABLE steam_users_temp
            (
                steam_user_id integer,
                steamid bigint,
                communityvisibilitystate smallint,
                profilestate smallint,
                personaname character varying(255),
                lastlogoff integer,
                profileurl text,
                avatar text,
                avatarmedium text,
                avatarfull text,
                personastate smallint,
                realname character varying(255),
                primaryclanid bigint,
                timecreated integer,
                personastateflags smallint,
                loccountrycode character varying(3),
                locstatecode character varying(3),
                loccityid integer,
                updated timestamp without time zone
            )
            ON COMMIT DROP;
        ");
    }
    
    public static function getNewRecordId() {
        $new_record_id = DB::selectOne("
            SELECT nextval('steam_users_seq'::regclass) AS id
        ");
        
        return $new_record_id->id;
    }
    
    public static function getTempInsertQueue(int $commit_count) {
        $record_queue = new RecordQueue($commit_count);
        
        $insert_queue = new InsertQueue("steam_users_temp");
        
        $insert_queue->addToRecordQueue($record_queue);
    
        return $record_queue;
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
            FROM steam_users_temp
        ");
    }
    
    public static function getAllIdsBySteamid() {
        $query = static::select([
            'steam_user_id',
            'steamid'
        ]);
        
        $ids_by_steamid = [];
        
        foreach($query->cursor() as $steam_user) {
            $ids_by_steamid[$steam_user->steamid] = $steam_user->steam_user_id;
        }
        
        return $ids_by_steamid;
    }
}
