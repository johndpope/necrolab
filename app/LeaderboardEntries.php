<?php

namespace App;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use App\Traits\GeneratesNewInstance;
use App\Traits\IsSchemaTable;
use App\Traits\HasPartitions;
use App\Traits\HasTempTable;
use App\Traits\HasCompositePrimaryKey;
use App\Traits\CanBeVacuumed;
use App\Dates;
use App\ExternalSites;
use App\LeaderboardSources;
use App\Leaderboards;
use App\LeaderboardsBlacklist;
use App\LeaderboardRankingTypes;
use App\LeaderboardSnapshots;
use App\Modes;
use App\Players;
use App\PlayerPbs;
use App\Characters;
use App\Releases;
use App\SeededTypes;
use App\MultiplayerTypes;
use App\Soundtracks;

class LeaderboardEntries extends Model {
    use GeneratesNewInstance, IsSchemaTable, HasPartitions, HasTempTable, HasCompositePrimaryKey, CanBeVacuumed;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'leaderboard_entries';

    /**
     * The primary key associated with the model.
     *
     * @var string
     */
    protected $primaryKey = [
        'leaderboard_snapshot_id',
        'player_pb_id'
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public static function createTemporaryTable(LeaderboardSources $leaderboard_source): void {
        DB::statement("
            CREATE TEMPORARY TABLE " . static::getTempTableName($leaderboard_source) . " (
                leaderboard_snapshot_id integer,
                player_pb_id integer,
                player_id integer,
                rank integer
            )
            ON COMMIT DROP;
        ");
    }

    public static function clear(LeaderboardSources $leaderboard_source, Dates $date): void {
        DB::delete("
            DELETE FROM " . static::getTableName($leaderboard_source, new DateTime($date->name)) . " le
            USING " . LeaderboardSnapshots::getSchemaTableName($leaderboard_source) . " ls
            WHERE le.leaderboard_snapshot_id = ls.id
                AND ls.date_id = :date_id
        ", [
            ':date_id' => $date->id
        ]);
    }

    public static function saveNewTemp(LeaderboardSources $leaderboard_source, DateTime $date): void {
        $temp_table_name = static::getTempTableName($leaderboard_source);

        DB::statement("
            CREATE INDEX {$temp_table_name}_pkey 
            ON {$temp_table_name} (leaderboard_snapshot_id, player_pb_id)
        ");

        DB::statement("
            DELETE FROM {$temp_table_name} a
            USING {$temp_table_name} b
            WHERE a.leaderboard_snapshot_id = b.leaderboard_snapshot_id
                AND a.player_pb_id = b.player_pb_id
                AND a.rank < b.rank
        ");

        DB::statement("
            INSERT INTO " . static::getTableName($leaderboard_source, $date) . " (
                leaderboard_snapshot_id,
                player_id,
                player_pb_id,
                rank
            )
            SELECT 
                leaderboard_snapshot_id,
                player_id,
                player_pb_id,
                rank
            FROM {$temp_table_name}
            ON CONFLICT (leaderboard_snapshot_id, player_pb_id) DO 
            UPDATE
                SET rank = excluded.rank
        ");
    }

    public static function updateFromTemp(LeaderboardSources $leaderboard_source): void {}

    public static function getFirstPlaceRanksByPlayerQuery(LeaderboardSources $leaderboard_source, Dates $date, ?string $player_id = NULL) {
        $query = DB::table('dummy')
            ->select([
                'le.player_id',
                'l.release_id',
                DB::raw('COUNT(le.player_pb_id) AS first_place_ranks')
            ])
            ->from(Leaderboards::getSchemaTableName($leaderboard_source) . ' AS l')
            ->join('leaderboard_types AS lt', 'lt.id', '=', 'l.leaderboard_type_id')
            ->join(LeaderboardSnapshots::getSchemaTableName($leaderboard_source) . ' AS ls', 'ls.leaderboard_id', '=', 'l.id')
            ->join(static::getTableName($leaderboard_source, new DateTime($date->name)) . " AS le", 'le.leaderboard_snapshot_id', '=', 'ls.id')
            ->where('lt.name', '!=', 'daily')
            ->where('ls.date_id', $date->id);

        if(!empty($player_id)) {
            $query->join(Players::getSchemaTableName($leaderboard_source) . ' AS p', 'p.id', '=', 'le.player_id')
                ->where('p.external_id', $player_id);
        }

        return $query->where('le.rank', 1)
            ->groupBy([
                'le.player_id',
                'l.release_id'
            ]);
    }

    public static function getLegacyImportQuery(LeaderboardSources $leaderboard_source, DateTime $date): Builder {
        $start_date = new DateTime($date->format('Y-m-01'));
        $end_date = new DateTime($date->format('Y-m-t'));

        $table_name = str_replace(
            "{$leaderboard_source->name}.",
            '',
            static::getTableName($leaderboard_source, $date)
        );

        return DB::table('leaderboard_snapshots AS ls')
            ->select([
                'l.lbid',
                'ls.leaderboard_snapshot_id',
                'le.steam_user_pb_id',
                'sup.steam_user_id',
                'le.rank'
            ])
            ->join('leaderboards AS l', 'l.leaderboard_id', '=', 'ls.leaderboard_id')
            ->join("{$table_name} AS le", 'le.leaderboard_snapshot_id', '=', 'ls.leaderboard_snapshot_id')
            ->join('steam_user_pbs AS sup', 'sup.steam_user_pb_id', '=', 'le.steam_user_pb_id')
            ->whereBetween('ls.date', [
                $start_date->format('Y-m-d'),
                $end_date->format('Y-m-d')
            ]);
    }

    public static function getPowerRankingsQuery(LeaderboardSources $leaderboard_source, Dates $date): Builder {
        return DB::table(LeaderboardSnapshots::getSchemaTableName($leaderboard_source) . ' AS ls')
            ->select([
                'le.player_pb_id',
                'ppb.player_id',
                'le.rank',
                'ppb.details',
                'lt.name AS leaderboard_type',
                'c.name AS character',
                'l.release_id',
                'l.mode_id',
                'l.seeded_type_id',
                'l.multiplayer_type_id',
                'l.soundtrack_id'
            ])
            ->join(Leaderboards::getSchemaTableName($leaderboard_source) . ' AS l', 'l.id', '=', 'ls.leaderboard_id')
            ->join('leaderboard_types AS lt', 'lt.id', '=', 'l.leaderboard_type_id')
            ->join('characters AS c', 'c.id', '=', 'l.character_id')
            ->join(LeaderboardRankingTypes::getSchemaTableName($leaderboard_source) . ' AS lrt', 'lrt.leaderboard_id', '=', 'l.id')
            ->join('ranking_types AS rt', 'rt.id', '=', 'lrt.ranking_type_id')
            ->join(static::getTableName($leaderboard_source, new DateTime($date->name)) . " AS le", 'le.leaderboard_snapshot_id', '=', 'ls.id')
            ->join(PlayerPbs::getSchemaTableName($leaderboard_source) . " AS ppb", 'ppb.id', '=', 'le.player_pb_id')
            ->where('ls.date_id', $date->id)
            ->where('rt.name', 'power');
    }

    public static function getDailyRankingsQuery(LeaderboardSources $leaderboard_source, DateTime $start_date, DateTime $end_date): Builder {
        $query = NULL;

        $table_names = static::getTableNames($leaderboard_source, $start_date, $end_date);

        if(!empty($table_names)) {
            foreach($table_names as $table_name) {
                $partition_query = DB::table(Leaderboards::getSchemaTableName($leaderboard_source) . ' AS l')
                    ->select([
                        'l.character_id',
                        'l.release_id',
                        'l.mode_id',
                        'l.multiplayer_type_id',
                        'l.soundtrack_id',
                        'd.name AS date',
                        'le.player_id',
                        'le.rank',
                        'ppb.is_win',
                        'ppb.details'
                    ])
                    ->join('dates AS d', 'd.id', '=', 'l.daily_date_id')
                    ->join(LeaderboardRankingTypes::getSchemaTableName($leaderboard_source) . ' AS lrt', 'lrt.leaderboard_id', '=', 'l.id')
                    ->join('ranking_types AS rt', 'rt.id', '=', 'lrt.ranking_type_id')
                    ->join(LeaderboardSnapshots::getSchemaTableName($leaderboard_source) . ' AS ls', function($join) {
                        $join->on('ls.leaderboard_id', '=', 'l.id');
                        $join->on('ls.date_id', '=', 'l.daily_date_id');
                    })
                    ->join("{$table_name} AS le", 'le.leaderboard_snapshot_id', '=', 'ls.id')
                    ->join(PlayerPbs::getSchemaTableName($leaderboard_source) . " AS ppb", 'ppb.id', '=', 'le.player_pb_id')
                    ->whereBetween('d.name', [
                        $start_date->format('Y-m-d'),
                        $end_date->format('Y-m-d')
                    ])
                    ->where('rt.name', 'daily');

                if(!isset($query)) {
                    $query = $partition_query;
                }
                else {
                    $query->unionAll($partition_query);
                }
            }
        }

        return $query;
    }

    public static function getStatsReadQuery(LeaderboardSources $leaderboard_source, Dates $date): Builder {
        return DB::table(LeaderboardSnapshots::getSchemaTableName($leaderboard_source) . ' AS ls')
            ->select([
                'ls.id AS leaderboard_snapshot_id',
                'ppb.details'
            ])
            ->join(static::getTableName($leaderboard_source, new DateTime($date->name)) . " AS le", 'le.leaderboard_snapshot_id', '=', 'ls.id')
            ->join(PlayerPbs::getSchemaTableName($leaderboard_source) . " AS ppb", 'ppb.id', '=', 'le.player_pb_id')
            ->where('ls.date_id', $date->id);
    }

    public static function getPlayerStatsQuery(LeaderboardSources $leaderboard_source, Dates $date): Builder {
        $player_ids_for_date_query = PlayerPbs::getPlayerIdsForDateQuery($leaderboard_source, $date);

        return DB::table(LeaderboardSnapshots::getSchemaTableName($leaderboard_source) . ' AS ls')
            ->select([
                'ppb.player_id',
                'le.rank',
                'lt.name AS leaderboard_type',
                'c.name AS character',
                'r.name AS release',
                'm.name AS mode',
                'st.name AS seeded_type',
                'mt.name AS multiplayer_type',
                's.name AS soundtrack'
            ])
            ->join(Leaderboards::getSchemaTableName($leaderboard_source) . ' AS l', 'l.id', '=', 'ls.leaderboard_id')
            ->join('leaderboard_types AS lt', 'lt.id', '=', 'l.leaderboard_type_id')
            ->join('characters AS c', 'c.id', '=', 'l.character_id')
            ->join('releases AS r', 'r.id', '=', 'l.release_id')
            ->join('modes AS m', 'm.id', '=', 'l.mode_id')
            ->join('seeded_types AS st', 'st.id', '=', 'l.seeded_type_id')
            ->join('multiplayer_types AS mt', 'mt.id', '=', 'l.multiplayer_type_id')
            ->join('soundtracks AS s', 's.id', '=', 'l.soundtrack_id')
            ->join(static::getTableName($leaderboard_source, new DateTime($date->name)) . " AS le", 'le.leaderboard_snapshot_id', '=', 'ls.id')
            ->join(PlayerPbs::getSchemaTableName($leaderboard_source) . " AS ppb", 'ppb.id', '=', 'le.player_pb_id')
            ->joinSub($player_ids_for_date_query, 'player_ids', 'player_ids.player_id', '=', 'ppb.player_id')
            ->where('ls.date_id', $date->id)
            ->where('lt.name', '!=', 'daily');
    }

    public static function getNonDailyCacheQuery(LeaderboardSources $leaderboard_source, Dates $date): Builder {
        $entries_table_name = static::getTableName($leaderboard_source, new DateTime($date->name));

        $query = DB::table(LeaderboardSnapshots::getSchemaTableName($leaderboard_source) . ' AS ls')
            ->select([
                'l.external_id AS leaderboard_id',
                'le.player_id',
                'le.rank'
            ])
            ->join(Leaderboards::getSchemaTableName($leaderboard_source) . ' AS l', 'l.id', '=', 'ls.leaderboard_id')
            ->join('leaderboard_types AS lt', 'lt.id', '=', 'l.leaderboard_type_id')
            ->leftJoin(LeaderboardsBlacklist::getSchemaTableName($leaderboard_source) . ' AS lb', 'lb.leaderboard_id', '=', 'l.id')
            ->join("{$entries_table_name} AS le", 'le.leaderboard_snapshot_id', '=', 'ls.id')
            ->leftJoin("user_{$leaderboard_source->name}_player AS up", 'up.player_id', '=', 'le.player_id')
            ->leftJoin('users AS u', 'u.id', '=', 'up.user_id')
            ->where('ls.date_id', $date->id)
            ->where('lt.name', '!=', 'daily')
            ->whereNull('lb.leaderboard_id');

        ExternalSites::addSiteIdSelectFields($query);

        return $query;
    }

    public static function getDailyCacheQuery(LeaderboardSources $leaderboard_source, Dates $date): Builder {
        $entries_table_name = static::getTableName($leaderboard_source, new DateTime($date->name));

        $query = DB::table(Leaderboards::getSchemaTableName($leaderboard_source) . ' AS l')
            ->select([
                'ls.leaderboard_id',
                'l.character_id',
                'l.release_id',
                'l.mode_id',
                'l.multiplayer_type_id',
                'l.soundtrack_id',
                'le.player_id',
                'le.rank'
            ])
            ->join('leaderboard_types AS lt', 'lt.id', '=', 'l.leaderboard_type_id')
            ->leftJoin(LeaderboardsBlacklist::getSchemaTableName($leaderboard_source) . ' AS lb', 'lb.leaderboard_id', '=', 'l.id')
            ->join(LeaderboardSnapshots::getSchemaTableName($leaderboard_source) . ' AS ls', function($join) {
                $join->on('ls.leaderboard_id', '=', 'l.id');
                $join->on('ls.date_id', '=', 'l.daily_date_id');
            })
            ->join("{$entries_table_name} AS le", 'le.leaderboard_snapshot_id', '=', 'ls.id')
            ->leftJoin("user_{$leaderboard_source->name}_player AS up", 'up.player_id', '=', 'le.player_id')
            ->leftJoin('users AS u', 'u.id', '=', 'up.user_id')
            ->where('l.daily_date_id', $date->id)
            ->where('lt.name', '=', 'daily')
            ->whereNull('lb.leaderboard_id');

        ExternalSites::addSiteIdSelectFields($query);

        return $query;
    }

    public static function getNonDailyApiReadQuery(LeaderboardSources $leaderboard_source, string $leaderboard_id, Dates $date): Builder {
        $entries_table_name = static::getTableName($leaderboard_source, new DateTime($date->name));

        $query = DB::table(Leaderboards::getSchemaTableName($leaderboard_source) . ' AS l')
            ->select([
                'le.rank'
            ])
            ->join(LeaderboardSnapshots::getSchemaTableName($leaderboard_source) . ' AS ls', 'ls.leaderboard_id', '=', 'l.id')
            ->join("{$entries_table_name} AS le", 'le.leaderboard_snapshot_id', '=', 'ls.id')
            ->join(Players::getSchemaTableName($leaderboard_source) . ' AS p', 'p.id', '=', 'le.player_id')
            ->join(PlayerPbs::getSchemaTableName($leaderboard_source) . ' AS ppb', 'ppb.id', '=', 'le.player_pb_id');

        Players::addSelects($query);
        PlayerPbs::addSelects($query);

        PlayerPbs::addJoins($leaderboard_source, $query);

        Players::addLeftJoins($leaderboard_source, $query);
        PlayerPbs::addLeftJoins($leaderboard_source, $query);

        $query->where('l.external_id', $leaderboard_id)
            ->where('ls.date_id', $date->id);

        return $query;
    }

    public static function getDailyApiReadQuery(
        LeaderboardSources $leaderboard_source,
        Characters $character,
        Releases $release,
        Modes $mode,
        MultiplayerTypes $multiplayer_type,
        Soundtracks $soundtrack,
        Dates $date
    ): Builder {
        $entries_table_name = static::getTableName($leaderboard_source, new DateTime($date->name));

        $query = DB::table(Leaderboards::getSchemaTableName($leaderboard_source) . ' AS l')
            ->select([
                'le.rank',
            ])
            ->join('seeded_types AS st', 'st.id', '=', 'l.seeded_type_id')
            ->join(LeaderboardSnapshots::getSchemaTableName($leaderboard_source) . ' AS ls', 'ls.leaderboard_id', '=', 'l.id')
            ->join("{$entries_table_name} AS le", 'le.leaderboard_snapshot_id', '=', 'ls.id')
            ->join(Players::getSchemaTableName($leaderboard_source) . ' AS p', 'p.id', '=', 'le.player_id')
            ->join(PlayerPbs::getSchemaTableName($leaderboard_source) . ' AS ppb', 'ppb.id', '=', 'le.player_pb_id');

        Players::addSelects($query);
        PlayerPbs::addSelects($query);

        PlayerPbs::addJoins($leaderboard_source, $query);

        Players::addLeftJoins($leaderboard_source, $query);
        PlayerPbs::addLeftJoins($leaderboard_source, $query);

        $query->where('lt.name', 'daily')
            ->where('l.character_id', $character->id)
            ->where('l.release_id', $release->id)
            ->where('l.mode_id', $mode->id)
            ->where('st.name', '=', 'seeded')
            ->where('l.multiplayer_type_id', $multiplayer_type->id)
            ->where('l.soundtrack_id', $soundtrack->id)
            ->where('l.daily_date_id', $date->id)
            ->where('ls.date_id', $date->id);

        return $query;
    }

    public static function getPlayerNonDailyApiQuery(
        string $player_id,
        LeaderboardSources $leaderboard_source,
        Releases $release,
        Modes $mode,
        SeededTypes $seeded_type,
        MultiplayerTypes $multiplayer_type,
        Soundtracks $soundtrack,
        Dates $date
    ): Builder {
        $entries_table_name = static::getTableName($leaderboard_source, new DateTime($date->name));

        $query = DB::table(Leaderboards::getSchemaTableName($leaderboard_source) . ' AS l')
            ->select([
                'c.name AS character_name',
                'lt.name AS leaderboard_type_name',
                'le.rank',
            ])
            ->join('characters AS c', 'c.id', '=', 'l.character_id')
            ->join(LeaderboardSnapshots::getSchemaTableName($leaderboard_source) . ' AS ls', 'ls.leaderboard_id', '=', 'l.id')
            ->join("{$entries_table_name} AS le", 'le.leaderboard_snapshot_id', '=', 'ls.id')
            ->join(Players::getSchemaTableName($leaderboard_source) . ' AS p', 'p.id', '=', 'le.player_id')
            ->join(PlayerPbs::getSchemaTableName($leaderboard_source) . ' AS ppb', 'ppb.id', '=', 'le.player_pb_id');

        PlayerPbs::addSelects($query);
        PlayerPbs::addJoins($leaderboard_source, $query);
        PlayerPbs::addLeftJoins($leaderboard_source, $query);

        $query->where('p.external_id', $player_id)
            ->where('l.release_id', $release->id)
            ->where('l.mode_id', $mode->id)
            ->where('l.seeded_type_id', $seeded_type->id)
            ->where('l.multiplayer_type_id', $multiplayer_type->id)
            ->where('l.soundtrack_id', $soundtrack->id)
            ->where('ls.date_id', $date->id)
            ->orderBy('c.sort_order', 'asc')
            ->orderBy('lt.id', 'asc');

        return $query;
    }

    public static function getPlayerNonDailyApiReadQuery(
        string $player_id,
        LeaderboardSources $leaderboard_source,
        Releases $release,
        Modes $mode,
        SeededTypes $seeded_type,
        MultiplayerTypes $multiplayer_type,
        Soundtracks $soundtrack,
        Dates $date
    ): Builder {
        $query = static::getPlayerNonDailyApiQuery(
            $player_id,
            $leaderboard_source,
            $release,
            $mode,
            $seeded_type,
            $multiplayer_type,
            $soundtrack,
            $date
        );

        $query->where('lt.name', '!=', 'daily');

        return $query;
    }

    public static function getPlayerCategoryApiReadQuery(
        string $player_id,
        LeaderboardSources $leaderboard_source,
        LeaderboardTypes $leaderboard_type,
        Releases $release,
        Modes $mode,
        SeededTypes $seeded_type,
        MultiplayerTypes $multiplayer_type,
        Soundtracks $soundtrack,
        Dates $date
    ): Builder {
        $query = static::getPlayerNonDailyApiQuery(
            $player_id,
            $leaderboard_source,
            $release,
            $mode,
            $seeded_type,
            $multiplayer_type,
            $soundtrack,
            $date
        );

        $query->where('l.leaderboard_type_id', $leaderboard_type->id);

        return $query;
    }

    public static function getPlayerDailyApiReadQuery(
        string $player_id,
        LeaderboardSources $leaderboard_source,
        Characters $character,
        Releases $release,
        Modes $mode,
        MultiplayerTypes $multiplayer_type,
        Soundtracks $soundtrack
    ): Builder {
        $query = DB::table(PlayerPbs::getSchemaTableName($leaderboard_source) . ' AS ppb')
            ->select([
                'd.name AS first_snapshot_date',
                'ppb.first_rank AS rank'
            ])
            ->join(Leaderboards::getSchemaTableName($leaderboard_source) . ' AS l', 'l.id', '=', 'ppb.leaderboard_id')
            ->join('seeded_types AS st', 'st.id', '=', 'l.seeded_type_id')
            ->join('dates AS d', 'd.id', '=', 'l.daily_date_id')
            ->join(Players::getSchemaTableName($leaderboard_source) . ' AS p', 'p.id', '=', 'ppb.player_id');

        PlayerPbs::addSelects($query);
        PlayerPbs::addJoins($leaderboard_source, $query);
        PlayerPbs::addLeftJoins($leaderboard_source, $query);

        $query->where('p.external_id', $player_id)
            ->where('lt.name', 'daily')
            ->where('l.character_id', $character->id)
            ->where('l.release_id', $release->id)
            ->where('l.mode_id', $mode->id)
            ->where('st.name', 'seeded')
            ->where('l.multiplayer_type_id', $multiplayer_type->id)
            ->where('l.soundtrack_id', $soundtrack->id)
            ->orderBy('d.name', 'desc');

        return $query;
    }
}
