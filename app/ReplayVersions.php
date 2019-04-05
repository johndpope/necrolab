<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use App\Traits\SchemaGetByName;
use App\Traits\GetById;
use App\Traits\IsSchemaTable;
use App\Traits\HasManualSequence;
use App\Traits\HasTempTable;
use App\LeaderboardSources;

class ReplayVersions extends Model {
    use SchemaGetByName, GetById, IsSchemaTable, HasManualSequence, HasTempTable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'replay_versions';
    
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
                name character varying(255)
            )
            ON COMMIT DROP;
        ");
    }
    
    public static function saveNewTemp(LeaderboardSources $leaderboard_source): void {
        DB::statement("
            INSERT INTO " . static::getSchemaTableName($leaderboard_source) . " (
                id,
                name
            )
            SELECT 
                id,
                name
            FROM " . static::getTempTableName($leaderboard_source) . "
        ");
    }
    
    public static function updateFromTemp(LeaderboardSources $leaderboard_source): void {}
    
    public static function getLegacyImportQuery(): Builder {
        return DB::table('steam_replay_versions')
            ->select([
                'steam_replay_version_id',
                'name'
            ]);
    }
}
