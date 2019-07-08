<?php

namespace App;

use stdClass;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use App\Traits\GeneratesNewInstance;
use App\Traits\IsSchemaTable;
use App\Traits\HasTempTable;
use App\Traits\CanBeVacuumed;
use App\LeaderboardSources;
use App\Seeds;

class Replays extends Model {
    use GeneratesNewInstance, IsSchemaTable, HasTempTable, CanBeVacuumed;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'replays';

    /**
     * The primary key associated with the model.
     *
     * @var string
     */
    protected $primaryKey = 'player_pb_id';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public static function getParsedReplayProperties(string $replay_file_data): ?object {
        $parsed_replay_properties = NULL;

        $replay_file_split = explode('%*#%*', $replay_file_data);

        if(count($replay_file_split) == 2) {
            $parsed_replay_properties = new stdClass();

            $parsed_replay_properties->run_result = $replay_file_split[0];

            if(empty($parsed_replay_properties->run_result)) {
                $parsed_replay_properties->is_win = 1;
                $parsed_replay_properties->run_result = 'WIN';
            }
            else {
                $parsed_replay_properties->is_win = 0;
            }

            $replay_data = $replay_file_split[1];

            $replay_data_segments = explode('\\n', $replay_data);

            $parsed_replay_properties->version = $replay_data_segments[0];

            if($parsed_replay_properties->version < 82) {
                $zone_1_seed = $replay_data_segments[10];

                $parsed_replay_properties->seed = Seeds::getSeedFromZ1Seed($zone_1_seed);
            }
            elseif($parsed_replay_properties->version < 84) {
                $zone_1_seed = $replay_data_segments[10];

                $parsed_replay_properties->seed = Seeds::getOldDLCSeedFromZ1Seed($zone_1_seed);
            }
            else {
                $zone_1_seed = $replay_data_segments[7];

                $parsed_replay_properties->seed = Seeds::getDLCSeedFromZ1Seed($zone_1_seed);
            }
        }

        return $parsed_replay_properties;
    }

    public static function createTemporaryTable(LeaderboardSources $leaderboard_source): void {
        DB::statement("
            CREATE TEMPORARY TABLE " . static::getTempTableName($leaderboard_source) . " (
                seed_id bigint,
                player_pb_id integer,
                player_id integer,
                run_result_id smallint,
                replay_version_id smallint,
                downloaded smallint,
                invalid smallint,
                uploaded_to_s3 smallint,
                external_id character varying(255)
            )
            ON COMMIT DROP;
        ");
    }

    public static function saveNewTemp(LeaderboardSources $leaderboard_source): void {
        DB::statement("
            INSERT INTO " . static::getSchemaTableName($leaderboard_source) . " (
                player_pb_id,
                external_id,
                player_id,
                downloaded,
                invalid,
                uploaded_to_s3
            )
            SELECT 
                player_pb_id,
                external_id,
                player_id,
                downloaded,
                invalid,
                uploaded_to_s3
            FROM " . static::getTempTableName($leaderboard_source) . "
        ");
    }

    public static function saveLegacyTemp(LeaderboardSources $leaderboard_source): void {
        DB::statement("
            INSERT INTO " . static::getSchemaTableName($leaderboard_source) . " (
                seed_id,
                player_pb_id,
                player_id,
                run_result_id,
                replay_version_id,
                downloaded,
                invalid,
                uploaded_to_s3,
                external_id
            )
            SELECT 
                seed_id,
                player_pb_id,
                player_id,
                run_result_id,
                replay_version_id,
                downloaded,
                invalid,
                uploaded_to_s3,
                external_id
            FROM " . static::getTempTableName($leaderboard_source) . "
        ");
    }

    public static function updateFromTemp(LeaderboardSources $leaderboard_source): void {}

    public static function updateDownloadedFromTemp(LeaderboardSources $leaderboard_source): void {
        DB::update("
            UPDATE " . static::getSchemaTableName($leaderboard_source) . " sr
            SET 
                downloaded = srt.downloaded,
                invalid = srt.invalid,
                run_result_id = srt.run_result_id,
                replay_version_id = srt.replay_version_id,
                seed_id = srt.seed_id
            FROM " . static::getTempTableName($leaderboard_source) . " srt
            WHERE sr.player_pb_id = srt.player_pb_id
        ");
    }

    public static function updateS3UploadedFromTemp(LeaderboardSources $leaderboard_source): void {
        DB::update("
            UPDATE " . static::getSchemaTableName($leaderboard_source) . " sr
            SET 
                uploaded_to_s3 = srt.uploaded_to_s3
            FROM " . static::getTempTableName($leaderboard_source) . " srt
            WHERE sr.player_pb_id = srt.player_pb_id
        ");
    }

    public static function getLegacyImportQuery(): Builder {
        return DB::table('steam_replays AS sr')
            ->select([
                'sup.steam_user_pb_id',
                'l.lbid',
                'sr.steam_user_id',
                'sr.ugcid',
                'sr.downloaded',
                'sr.invalid',
                'sr.seed',
                'rr.name AS run_result',
                'sr.steam_replay_version_id',
                'sr.uploaded_to_s3'
            ])
            ->join('run_results AS rr', 'rr.run_result_id', '=', 'sr.run_result_id')
            ->join('steam_user_pbs AS sup', 'sup.steam_replay_id', '=', 'sr.steam_replay_id')
            ->join('leaderboards AS l', 'l.leaderboard_id', '=', 'sup.leaderboard_id');
    }

    public static function getUnsavedQuery(LeaderboardSources $leaderboard_source): Builder {
        return DB::table(static::getSchemaTableName($leaderboard_source))
            ->where('downloaded', 0)
            ->where('invalid', 0)
            ->limit(1000);
    }

    public static function getNotS3UploadedQuery(LeaderboardSources $leaderboard_source): Builder {
        return DB::table(static::getSchemaTableName($leaderboard_source))
            ->where('uploaded_to_s3', 0)
            ->where('downloaded', 1)
            ->where('invalid', 0);
    }
}
