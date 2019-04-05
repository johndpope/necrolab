<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use App\Traits\IsSchemaTable;
use App\Traits\SchemaGetByName;
use App\Traits\GetById;
use App\Traits\HasManualSequence;
use App\Traits\HasTempTable;
use App\LeaderboardSources;

class RunResults extends Model {
    use IsSchemaTable, SchemaGetByName, GetById, HasManualSequence, HasTempTable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'run_results';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    
    public static function createTemporaryTable(LeaderboardSources $leaderboard_source): void {
        DB::statement("
            CREATE TEMPORARY TABLE " . static::getTempTableName($leaderboard_source) . " (
                id smallint,
                is_win smallint,
                name character varying(255)
            )
            ON COMMIT DROP;
        ");
    }
    
    public static function saveNewTemp(LeaderboardSources $leaderboard_source): void {    
        DB::statement("
            INSERT INTO " . static::getSchemaTableName($leaderboard_source) . " (
                id,
                is_win,
                name
            )
            SELECT 
                id,
                is_win,
                name
            FROM " . static::getTempTableName($leaderboard_source) . "
        ");
    }
    
    public static function updateFromTemp(LeaderboardSources $leaderboard_source) {}
    
    public static function getLegacyImportQuery(): Builder {
        return DB::table('run_results')
            ->select([
                DB::raw('MIN(run_result_id) AS run_result_id'),
                'name',
                DB::raw('min(is_win) AS is_win')
            ])
            ->groupBy('name');
    }
}
