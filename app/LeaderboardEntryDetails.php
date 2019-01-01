<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Traits\GetByName;
use App\Traits\HasTempTable;
use App\Traits\HasManualSequence;

class LeaderboardEntryDetails extends Model {
    use GetByName, HasTempTable, HasManualSequence;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'leaderboard_entry_details';
    
    /**
     * The primary key associated with the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    
    public static function createTemporaryTable() {
        DB::statement("
            CREATE TEMPORARY TABLE leaderboard_entry_details_temp (
                id smallint,
                \"name\" character varying(25)
            )
            ON COMMIT DROP;
        ");
    }
    
    public static function saveTemp() {
        DB::statement("
            INSERT INTO leaderboard_entry_details (
                id, 
                name
            )
            SELECT 
                id, 
                name
            FROM leaderboard_entry_details_temp
        ");
    } 
}
