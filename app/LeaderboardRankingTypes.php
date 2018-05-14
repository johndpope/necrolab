<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Traits\HasTempTable;

class LeaderboardRankingTypes extends Model {
    use HasTempTable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'leaderboard_ranking_types';
    
    /**
     * The primary key associated with the model.
     *
     * @var string
     */
    protected $primaryKey = [
        'leaderboard_id',
        'ranking_type_id'
    ];
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    
    public static function createTemporaryTable() {    
        DB::statement("
            CREATE TEMPORARY TABLE leaderboard_ranking_types_temp (
                leaderboard_id integer,
                ranking_type_id smallint
            )
            ON COMMIT DROP;
        ");
    }
    
    public static function saveTemp() {
        DB::statement("
            INSERT INTO leaderboard_ranking_types (
                leaderboard_id, 
                ranking_type_id
            )
            SELECT 
                leaderboard_id, 
                ranking_type_id
            FROM leaderboard_ranking_types_temp
        ");
    }
}