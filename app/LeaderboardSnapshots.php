<?php

namespace App;

use DatePeriod;
use DateTime;
use DateInterval;
use stdClass;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use App\Components\PostgresCursor;
use App\Traits\GeneratesNewInstance;
use App\Traits\IsSchemaTable;
use App\Traits\HasTempTable;
use App\Traits\HasManualSequence;
use App\Traits\CanBeVacuumed;
use App\LeaderboardSources;
use App\LeaderboardEntries;
use App\Dates;
use App\Leaderboards;
use App\PlayerPbs;
use App\Players;

class LeaderboardSnapshots extends Model {
    use GeneratesNewInstance, IsSchemaTable, HasTempTable, HasManualSequence, CanBeVacuumed;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'leaderboard_snapshots';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public static function createTemporaryTable(LeaderboardSources $leaderboard_source): void {
        DB::statement("
            CREATE TEMPORARY TABLE " . static::getTempTableName($leaderboard_source) . " (
                created timestamp without time zone,
                updated timestamp without time zone,
                id integer,
                leaderboard_id integer,
                players integer,
                date_id smallint,
                details jsonb
            )
            ON COMMIT DROP;
        ");
    }

    public static function saveNewTemp(LeaderboardSources $leaderboard_source): void {
        DB::statement("
            INSERT INTO " . static::getSchemaTableName($leaderboard_source) . " (
                created,
                updated,
                id,
                leaderboard_id,
                date_id
            )
            SELECT 
                created,
                updated,
                id,
                leaderboard_id,
                date_id
            FROM " . static::getTempTableName($leaderboard_source) . "
            ON CONFLICT (id) DO 
            UPDATE 
            SET 
                updated = excluded.updated
        ");
    }

    public static function updateFromTemp(LeaderboardSources $leaderboard_source): void {
        DB::update("
            UPDATE " . static::getSchemaTableName($leaderboard_source) . " ls
            SET 
                players = lst.players,
                details = lst.details
            FROM " . static::getTempTableName($leaderboard_source) . " lst
            WHERE ls.id = lst.id
        ");
    }

    public static function getAllByLeaderboardIdForDate(LeaderboardSources $leaderboard_source, Dates $date): array {
        $query = DB::table(static::getSchemaTableName($leaderboard_source))->where('date_id', $date->id);

        $cursor = new PostgresCursor(
            "{$leaderboard_source->name}_leaderboard_snapshots_by_id",
            $query,
            1000
        );

        $snapshots_by_leaderboard_id = [];

        foreach($cursor->getRecord() as $snapshot) {
            $snapshots_by_leaderboard_id[$snapshot->leaderboard_id] = $snapshot->id;
        }

        return $snapshots_by_leaderboard_id;
    }

    public static function getLegacyImportQuery(): Builder {
        return DB::table('leaderboard_snapshots AS ls')
            ->select([
                'ls.leaderboard_snapshot_id',
                'ls.leaderboard_id',
                'ls.created',
                'ls.date',
                'ls.updated',
                'l.is_daily',
                'l.daily_date'
            ])
            ->join('leaderboards AS l', 'l.leaderboard_id', '=', 'ls.leaderboard_id')
            ->join('characters AS c', 'c.character_id', '=', 'l.character_id')
            ->join('releases AS r', 'r.release_id', '=', 'l.release_id')
            ->join('modes AS m', 'm.mode_id', '=', 'l.mode_id');
    }

    public static function getApiReadQuery(LeaderboardSources $leaderboard_source, string $leaderboard_id): Builder {
        return DB::table(Leaderboards::getSchemaTableName($leaderboard_source) . ' AS l')
            ->select([
                'd.name AS date',
                'ls.players',
                'ls.details'
            ])
            ->join(static::getSchemaTableName($leaderboard_source) . ' AS ls', 'ls.leaderboard_id', '=', 'l.id')
            ->join('dates AS d', 'd.id', '=', 'ls.date_id')
            ->where('l.external_id', $leaderboard_id)
            ->orderBy('d.name', 'desc');
    }

    public static function getPlayerApiDates(string $player_id, LeaderboardSources $leaderboard_source, string $leaderboard_id): Collection {
        // Attempt to look up the earliest snapshot that this player has an entry for via their PBs.
        $earliest_snapshot = DB::table(PlayerPbs::getSchemaTableName($leaderboard_source) . ' AS ppb')
            ->selectRaw("
                ppb.leaderboard_id,
                l.release_id,
                MIN(d.name) AS first_snapshot_date,
                r.end_date AS release_end_date
            ")
            ->join(Players::getSchemaTableName($leaderboard_source) . ' AS p', 'p.id', '=', 'ppb.player_id')
            ->join(Leaderboards::getSchemaTableName($leaderboard_source) . ' AS l', 'l.id', '=', 'ppb.leaderboard_id')
            ->join('releases AS r', 'r.id', '=', 'l.release_id')
            ->join(static::getSchemaTableName($leaderboard_source) . ' AS ls', 'ls.id', '=', 'ppb.first_leaderboard_snapshot_id')
            ->join('dates AS d', 'd.id', '=', 'ls.date_id')
            ->where('p.external_id', $player_id)
            ->where('l.external_id', $leaderboard_id)
            ->groupBy(
                'ppb.leaderboard_id',
                'l.release_id',
                'r.end_date'
            )
            ->first();

        $snapshot_dates = [];

        /*
            If a record is returned from the lookup generate the remaining dates
            to either the end date of the release or today's date if the release is still active.
        */
        if(!empty($earliest_snapshot)) {
            $start_date = new DateTime($earliest_snapshot->first_snapshot_date);
            $end_date = new DateTime($earliest_snapshot->release_end_date);

            // Make the end date inclusive
            $end_date->modify('+1 day');

            $snapshot_date_range = new DatePeriod(
                $start_date,
                new DateInterval('P1D'),
                $end_date
            );

            foreach($snapshot_date_range as $snapshot_date) {
                $record = new stdClass();

                $record->date = $snapshot_date->format('Y-m-d');

                $snapshot_dates[] = $record;
            }

            rsort($snapshot_dates);
        }

        return collect($snapshot_dates);
    }
}
