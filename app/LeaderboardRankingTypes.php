<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Traits\IsSchemaTable;
use App\Traits\HasTempTable;
use App\LeaderboardSources;

class LeaderboardRankingTypes extends Model {
    use IsSchemaTable, HasTempTable;

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
    
    public static function createTemporaryTable(LeaderboardSources $leaderboard_source) {    
        DB::statement("
            CREATE TEMPORARY TABLE " . static::getTempTableName($leaderboard_source) . " (
                leaderboard_id integer,
                ranking_type_id smallint
            )
            ON COMMIT DROP;
        ");
    }
    
    public static function saveNewTemp(LeaderboardSources $leaderboard_source) {
        DB::statement("
            INSERT INTO " . static::getSchemaTableName($leaderboard_source) . " (
                leaderboard_id, 
                ranking_type_id
            )
            SELECT 
                leaderboard_id, 
                ranking_type_id
            FROM " . static::getTempTableName($leaderboard_source) . "
        ");
    }
    
    public static function updateFromTemp(LeaderboardSources $leaderboard_source) {}
}
