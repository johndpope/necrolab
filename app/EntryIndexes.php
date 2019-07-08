<?php

namespace App;

use PDO;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Components\Encoder;
use App\Traits\GeneratesNewInstance;
use App\Traits\IsSchemaTable;
use App\Traits\HasTempTable;
use App\Traits\HasCompositePrimaryKey;
use App\Traits\CanBeVacuumed;
use App\LeaderboardSources;

class EntryIndexes extends Model {
    use GeneratesNewInstance, IsSchemaTable, HasTempTable, HasCompositePrimaryKey, CanBeVacuumed;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'entry_indexes';

    /**
     * The primary key associated with the model.
     *
     * @var string
     */
    protected $primaryKey = [
        'name',
        'sub_name'
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public static function getTempInsertQueueBindFlags() {
        return [
            PDO::PARAM_LOB,
            PDO::PARAM_STR,
            PDO::PARAM_STR
        ];
    }

    public static function createTemporaryTable(LeaderboardSources $leaderboard_sources) {
        DB::statement("
            CREATE TEMPORARY TABLE " . static::getTempTableName($leaderboard_sources) . " (
                data bytea NOT NULL,
                name character varying(255) NOT NULL,
                sub_name character varying(255) NOT NULL
            )
            ON COMMIT DROP;
        ");
    }

    public static function saveNewTemp(LeaderboardSources $leaderboard_sources) {
        DB::statement("
            INSERT INTO " . static::getSchemaTableName($leaderboard_sources) . " (
                data,
                name,
                sub_name
            )
            SELECT 
                data,
                name,
                sub_name
            FROM " . static::getTempTableName($leaderboard_sources) . "
            ON CONFLICT (name, sub_name) DO 
            UPDATE 
            SET 
                data = excluded.data
        ");
    }

    public static function updateFromTemp(LeaderboardSources $leaderboard_source) {}

    public static function getDecodedRecord(LeaderboardSources $leaderboard_source, string $name, string $sub_name = '') {
        $cache_key_name = "{$leaderboard_source->name}:{$name}:{$sub_name}";

        return Cache::store('opcache')->remember($cache_key_name, 5, function() use($leaderboard_source, $name, $sub_name) {
            $encoded_data = DB::table(static::getSchemaTableName($leaderboard_source))->where('name', $name)
                ->where('sub_name', $sub_name)
                ->first();

            $decoded_data = [];

            if(!empty($encoded_data)) {
                $decoded_data = Encoder::decode(stream_get_contents($encoded_data->data));
            }

            return $decoded_data;
        });
    }
}
