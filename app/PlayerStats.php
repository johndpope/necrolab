<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use App\Traits\GeneratesNewInstance;
use App\Traits\IsSchemaTable;
use App\Traits\HasTempTable;
use App\Traits\HasManualSequence;
use App\Traits\AddsSqlCriteria;
use App\Traits\CanBeVacuumed;
use App\LeaderboardSources;
use App\Players;
use App\Dates;
use App\Releases;

class PlayerStats extends Model {
    use GeneratesNewInstance, IsSchemaTable, HasTempTable, HasManualSequence, AddsSqlCriteria, CanBeVacuumed;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'player_stats';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public static function createTemporaryTable(LeaderboardSources $leaderboard_source): void {
        DB::statement("
            CREATE TEMPORARY TABLE " . static::getTempTableName($leaderboard_source) . "
            (
                player_id integer,
                date_id smallint,
                release_id smallint,
                pbs smallint,
                leaderboards smallint,
                first_place_ranks smallint,
                dailies smallint,
                leaderboard_types jsonb,
                characters jsonb,
                modes jsonb,
                seeded_types jsonb,
                multiplayer_types jsonb,
                soundtracks jsonb,
                hash character varying(8),
                details jsonb
            )
            ON COMMIT DROP;
        ");
    }

    public static function clear(LeaderboardSources $leaderboard_source, Dates $date): void {
        static::setSchemaStatic($leaderboard_source->name)
            ->where('date_id', $date->id)
            ->delete();
    }

    public static function saveNewTemp(LeaderboardSources $leaderboard_source): void {
        DB::statement("
            INSERT INTO " . static::getSchemaTableName($leaderboard_source) . " (
                player_id,
                date_id,
                release_id,
                pbs,
                leaderboards,
                first_place_ranks,
                dailies,
                leaderboard_types,
                characters,
                modes,
                seeded_types,
                multiplayer_types,
                soundtracks,
                hash,
                details
            )
            SELECT 
                player_id,
                date_id,
                release_id,
                pbs,
                leaderboards,
                first_place_ranks,
                dailies,
                leaderboard_types,
                characters,
                modes,
                seeded_types,
                multiplayer_types,
                soundtracks,
                hash,
                details
            FROM " . static::getTempTableName($leaderboard_source) . "
            ON CONFLICT (player_id, release_id, \"hash\")
            DO NOTHING
        ");
    }

    public static function updateFromTemp(LeaderboardSources $leaderboard_source): void {}

    public static function addOverallToTemp(LeaderboardSources $leaderboard_source) {
        $details_key_aggregation_query = DB::table('dummy')
            ->select([
                'player_id',
                'date_id',
                'details.key',
                DB::raw('SUM(details.value::numeric) AS value')
            ])
            ->from(static::getTempTableName($leaderboard_source))
            ->crossJoin(DB::raw('LATERAL jsonb_each_text(details) AS details'))
            ->groupBy([
                'player_id',
                'date_id',
                'details.key'
            ]);

        $details_aggregation_query = DB::table('dummy')
            ->select([
                'player_id',
                'date_id',
                DB::raw('json_object_agg(key, value) AS details')
            ])
            ->fromSub($details_key_aggregation_query, 'details_keys')
            ->groupBy([
                'player_id',
                'date_id'
            ]);

        $query = DB::table('dummy')
            ->select([
                'details.player_id',
                'details.date_id',
                DB::raw('SUM(ps.pbs) AS pbs'),
                DB::raw('SUM(ps.leaderboards) AS leaderboards'),
                DB::raw('SUM(ps.first_place_ranks) AS first_place_ranks'),
                DB::raw('SUM(ps.dailies) AS dailies'),
                DB::raw('json_agg(details.details)->0 AS details')
            ])
            ->fromSub($details_aggregation_query, 'details')
            ->join(static::getTempTableName($leaderboard_source) . ' AS ps', 'ps.player_id', '=', 'details.player_id')
            ->groupBy([
                'details.player_id',
                'details.date_id'
            ]);

        DB::statement("
            INSERT INTO " . static::getTempTableName($leaderboard_source) . " (
                player_id,
                date_id,
                pbs,
                leaderboards,
                first_place_ranks,
                dailies,
                details
            )
            {$query->toSql()}
        ", $query->getBindings());
    }

    public static function getPlayerApiReadQuery(string $player_id, LeaderboardSources $leaderboard_source): Builder {
        $query = DB::table(static::getSchemaTableName($leaderboard_source) . ' AS ps')
            ->select([
                'd.name AS date',
                'ps.pbs',
                'ps.leaderboards',
                'ps.first_place_ranks',
                'ps.dailies',
                'ps.leaderboard_types',
                'ps.characters',
                'ps.modes',
                'ps.seeded_types',
                'ps.multiplayer_types',
                'ps.soundtracks',
                'ps.details'
            ])
            ->join(Players::getSchemaTableName($leaderboard_source) . ' AS p', 'p.id', 'ps.player_id')
            ->join('dates AS d', 'd.id', '=', 'ps.date_id')
            ->where('p.external_id', $player_id)
            ->whereNull('ps.release_id')
            ->orderBy('d.name', 'desc');

        return $query;
    }

    public static function getPlayerLatestApiReadQuery(string $player_id, LeaderboardSources $leaderboard_source): Builder {
        return static::getPlayerApiReadQuery($player_id, $leaderboard_source)
            ->limit(1);
    }

    public static function getPlayerByReleaseApiReadQuery(string $player_id, LeaderboardSources $leaderboard_source, Releases $release): Builder {
        $query = DB::table(static::getSchemaTableName($leaderboard_source) . ' AS ps')
            ->select([
                'd.name AS date',
                'ps.pbs',
                'ps.leaderboards',
                'ps.first_place_ranks',
                'ps.dailies',
                'ps.leaderboard_types',
                'ps.characters',
                'ps.modes',
                'ps.seeded_types',
                'ps.multiplayer_types',
                'ps.soundtracks',
                'ps.details'
            ])
            ->join(Players::getSchemaTableName($leaderboard_source) . ' AS p', 'p.id', 'ps.player_id')
            ->join('dates AS d', 'd.id', '=', 'ps.date_id')
            ->where('p.external_id', $player_id)
            ->where('ps.release_id', $release->id)
            ->orderBy('d.name', 'desc');

        return $query;
    }
}
