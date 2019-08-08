<?php

namespace App;

use DateTime;
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
use App\LeaderboardTypes;
use App\Players;
use App\Dates;
use App\Releases;
use App\RankPoints;

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
                seeded_pbs smallint,
                unseeded_pbs smallint,
                best_leaderboard_type_id smallint,
                best_character_id smallint,
                best_release_id smallint,
                best_mode_id smallint,
                best_seeded_type_id smallint,
                best_multiplayer_type_id smallint,
                best_soundtrack_id smallint,
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
                seeded_pbs,
                unseeded_pbs,
                best_leaderboard_type_id,
                best_character_id,
                best_release_id,
                best_mode_id,
                best_seeded_type_id,
                best_multiplayer_type_id,
                best_soundtrack_id,
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
                seeded_pbs,
                unseeded_pbs,
                best_leaderboard_type_id,
                best_character_id,
                best_release_id,
                best_mode_id,
                best_seeded_type_id,
                best_multiplayer_type_id,
                best_soundtrack_id,
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

    public static function getPlayerLatest(LeaderboardSources $leaderboard_source, string $player_id, ?Releases $release = NULL): array {
        // Get today's date
        $current_date = Dates::getByName((new DateTime())->format('Y-m-d'));

        $query = DB::table('dummy')
            ->select([
                'lt.name AS leaderboard_type',
                'c.name AS character',
                'r.name AS release',
                'm.name AS mode',
                'st.name AS seeded_type',
                'mt.name AS multiplayer_type',
                's.name AS soundtrack',
                'le.rank'
            ])
            ->from(Leaderboards::getSchemaTableName($leaderboard_source) . ' AS l')
            ->join('leaderboard_types AS lt', 'lt.id', '=', 'l.leaderboard_type_id')
            ->join('characters as c', 'c.id', '=', 'l.character_id')
            ->join('releases as r', 'r.id', '=', 'l.release_id')
            ->join('modes as m', 'm.id', '=', 'l.mode_id')
            ->join('seeded_types as st', 'st.id', '=', 'l.seeded_type_id')
            ->join('multiplayer_types as mt', 'mt.id', '=', 'l.multiplayer_type_id')
            ->join('soundtracks as s', 's.id', '=', 'l.soundtrack_id')
            ->join(LeaderboardSnapshots::getSchemaTableName($leaderboard_source) . ' AS ls', 'ls.leaderboard_id', '=', 'l.id')
            ->join(LeaderboardEntries::getTableName($leaderboard_source, new DateTime($current_date->name)) . " AS le", 'le.leaderboard_snapshot_id', '=', 'ls.id')
            ->join(PlayerPbs::getSchemaTableName($leaderboard_source) . ' AS ppb', 'ppb.id', '=', 'le.player_pb_id')
            ->join(Players::getSchemaTableName($leaderboard_source) . ' AS p', 'p.id', '=', 'ppb.player_id')
            ->where('lt.name', '!=', 'daily');

        if(!empty($release)) {
            $query->where('l.release_id', $release->id);
        }

        $query->where('ls.date_id', $current_date->id)
            ->where('p.external_id', $player_id);

        $attribute_points = [
            'leaderboard_type' => [],
            'character' => [],
            'release' => [],
            'mode' => [],
            'seeded_type' => [],
            'multiplayer_type' => [],
            'soundtrack' => []
        ];

        $attributes = array_keys($attribute_points);

        $first_place_ranks = 0;

        foreach($query->cursor() as $leaderboard_entry) {
            $rank_points = RankPoints::calculateFromRank($leaderboard_entry->rank);

            foreach($attributes as $attribute_name) {
                if(!isset($attribute_points[$attribute_name][$leaderboard_entry->$attribute_name])) {
                    $attribute_points[$attribute_name][$leaderboard_entry->$attribute_name] = 0;
                }

                $attribute_points[$attribute_name][$leaderboard_entry->$attribute_name] += $rank_points;
            }

            if($leaderboard_entry->rank == 1) {
                $first_place_ranks += 1;
            }
        }

        $latest_stats = [
            'date' => $current_date->name,
            'first_place_ranks' => $first_place_ranks
        ];

        foreach($attribute_points as $attribute_name => $points) {
            arsort($points);
            reset($points);

            $best_name = "best_{$attribute_name}";

            $latest_stats[$best_name] = key($points);
        }

        return $latest_stats;
    }

    public static function getPlayerApiReadQuery(string $player_id, LeaderboardSources $leaderboard_source): Builder {
        $query = DB::table(static::getSchemaTableName($leaderboard_source) . ' AS ps')
            ->select([
                'd.name AS date',
                'ps.pbs',
                'ps.leaderboards',
                'ps.first_place_ranks',
                'ps.dailies',
                'ps.seeded_pbs',
                'ps.unseeded_pbs',
                'lt.name AS best_leaderboard_type',
                'c.name AS best_character',
                'r.name AS best_release',
                'm.name AS best_mode',
                'st.name AS best_seeded_type',
                'mt.name AS best_multiplayer_type',
                's.name AS best_soundtrack',
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
            ->leftJoin('leaderboard_types AS lt', 'lt.id', '=', 'ps.best_leaderboard_type_id')
            ->leftJoin('characters AS c', 'c.id', '=', 'ps.best_character_id')
            ->leftJoin('releases AS r', 'r.id', '=', 'ps.best_release_id')
            ->leftJoin('modes AS m', 'm.id', '=', 'ps.best_mode_id')
            ->leftJoin('seeded_types AS st', 'st.id', '=', 'ps.best_seeded_type_id')
            ->leftJoin('multiplayer_types AS mt', 'mt.id', '=', 'ps.best_multiplayer_type_id')
            ->leftJoin('soundtracks AS s', 's.id', '=', 'ps.best_soundtrack_id')
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
                'ps.seeded_pbs',
                'ps.unseeded_pbs',
                'lt.name AS best_leaderboard_type',
                'c.name AS best_character',
                'm.name AS best_mode',
                'st.name AS best_seeded_type',
                'mt.name AS best_multiplayer_type',
                's.name AS best_soundtrack',
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
            ->leftJoin('leaderboard_types AS lt', 'lt.id', '=', 'ps.best_leaderboard_type_id')
            ->leftJoin('characters AS c', 'c.id', '=', 'ps.best_character_id')
            ->leftJoin('modes AS m', 'm.id', '=', 'ps.best_mode_id')
            ->leftJoin('seeded_types AS st', 'st.id', '=', 'ps.best_seeded_type_id')
            ->leftJoin('multiplayer_types AS mt', 'mt.id', '=', 'ps.best_multiplayer_type_id')
            ->leftJoin('soundtracks AS s', 's.id', '=', 'ps.best_soundtrack_id')
            ->where('p.external_id', $player_id)
            ->where('ps.release_id', $release->id)
            ->orderBy('d.name', 'desc');

        return $query;
    }
}
