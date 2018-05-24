<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Traits\GetByName;
use App\Traits\GetById;
use App\Traits\HasManualSequence;
use App\Traits\HasTempTable;

class SteamReplayVersions extends Model {
    use GetByName, GetById, HasManualSequence, HasTempTable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'steam_replay_versions';
    
    /**
     * The primary key associated with the model.
     *
     * @var string
     */
    protected $primaryKey = 'steam_replay_version_id';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    
    public static function createTemporaryTable() {
        DB::statement("
            CREATE TEMPORARY TABLE " . static::getTempTableName() . " (
                steam_replay_version_id smallint,
                name character varying(255)
            )
            ON COMMIT DROP;
        ");
    }
    
    public static function saveNewFromTemp() {
        DB::statement("
            INSERT INTO steam_replay_versions (
                steam_replay_version_id,
                name
            )
            SELECT 
                steam_replay_version_id,
                name
            FROM " . static::getTempTableName() . "
        ");
    }
}
