<?php

namespace App;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Query\Builder;
use App\Components\PostgresCursor;
use App\Traits\GeneratesNewInstance;
use App\Traits\IsSchemaTable;
use App\Traits\HasTempTable;
use App\Traits\HasManualSequence;
use App\Traits\AddsSqlCriteria;
use App\Traits\CanBeVacuumed;
use App\ExternalSites;
use App\LeaderboardSources;

class Players extends Model {
    use GeneratesNewInstance, IsSchemaTable, HasTempTable, HasManualSequence, AddsSqlCriteria, CanBeVacuumed;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'players';

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
                created timestamp without time zone,
                updated timestamp without time zone,
                id integer,
                external_id character varying(255),
                username character varying(255),
                profile_url text,
                avatar_url text
            )
            ON COMMIT DROP;
        ");
    }

    public static function saveNewTemp(LeaderboardSources $leaderboard_source): void {
        DB::statement("
            INSERT INTO " . static::getSchemaTableName($leaderboard_source) . " (
                id, 
                external_id, 
                created,
                updated
            )
            SELECT 
                id,
                external_id,
                created,
                updated
            FROM " . static::getTempTableName($leaderboard_source) . "
        ");
    }

    public static function updateFromTemp(LeaderboardSources $leaderboard_source): void {
        DB::update("
            UPDATE " . static::getSchemaTableName($leaderboard_source) . " p
            SET 
                updated = pt.updated,
                username = pt.username,
                username_search_index = to_tsvector(pt.username),
                profile_url = pt.profile_url,
                avatar_url = pt.avatar_url
            FROM " . static::getTempTableName($leaderboard_source) . " pt
            WHERE p.id = pt.id
        ");
    }

    public static function saveLegacyTemp(LeaderboardSources $leaderboard_source): void {
        DB::update("
            INSERT INTO " . static::getSchemaTableName($leaderboard_source) . " (
                created,
                updated,
                id,
                external_id,
                username,
                username_search_index,
                profile_url,
                avatar_url
            )
            SELECT
                created,
                updated,
                id, 
                external_id,
                username,
                to_tsvector(username),
                profile_url,
                avatar_url
            FROM " . static::getTempTableName($leaderboard_source) . "
        ");
    }

    public static function updateRecordSearchIndex(LeaderboardSources $leaderboard_source, \App\Players $record): void {
        DB::update("
            UPDATE " . static::getSchemaTableName($leaderboard_source) . "
            SET username_search_index = to_tsvector(:username)
            WHERE id = :id
        ", [
            ':username' => $record->username,
            ':id' => $record->id
        ]);
    }

    public static function addSelects(Builder $query) {
        $query->addSelect([
            'u.id AS necrolab_id',
            'u.name AS necrolab_username',
            'p.external_id AS player_id',
            'p.username AS player_username',
            'p.profile_url AS player_profile_url',
            'mu.external_id AS mixer_id',
            'mu.username AS mixer_username',
            'du.external_id AS discord_id',
            'du.username AS discord_username',
            'du.discriminator AS discord_discriminator',
            'ru.external_id AS reddit_id',
            'ru.username AS reddit_username',
            'tu.external_id AS twitch_id',
            'tu.user_display_name AS twitch_username',
            'twu.external_id AS twitter_id',
            'twu.nickname AS twitter_nickname',
            'twu.name AS twitter_name',
            'yu.external_id AS youtube_id',
            'yu.external_id AS youtube_username'
        ]);
    }

    public static function addLeftJoins(LeaderboardSources $leaderboard_source, Builder $query): void {
        $query->leftJoin("user_{$leaderboard_source->name}_player AS up", 'up.player_id', '=', 'p.id');
        $query->leftJoin('users AS u', 'u.id', '=', 'up.user_id');
        $query->leftJoin('mixer_users AS mu', 'mu.id', '=', 'u.mixer_user_id');
        $query->leftJoin('discord_users AS du', 'du.id', '=', 'u.discord_user_id');
        $query->leftJoin('reddit_users AS ru', 'ru.id', '=', 'u.reddit_user_id');
        $query->leftJoin('twitch_users AS tu', 'tu.id', '=', 'u.twitch_user_id');
        $query->leftJoin('twitter_users AS twu', 'twu.id', '=', 'u.twitter_user_id');
        $query->leftJoin('youtube_users AS yu', 'yu.id', '=', 'u.youtube_user_id');
    }

    public static function getAllIdsByPlayerid(LeaderboardSources $leaderboard_source): array {
        $query = DB::table(static::getSchemaTableName($leaderboard_source))
            ->select([
                'id',
                'external_id'
            ]);

        $cursor = new PostgresCursor(
            "{$leaderboard_source->name}_player_ids",
            $query,
            20000
        );

        $ids_by_player_id = [];

        foreach($cursor->getRecord() as $player) {
            $ids_by_player_id[$player->external_id] = $player->id;
        }

        return $ids_by_player_id;
    }

    public static function getLegacyImportQuery(): \Illuminate\Database\Query\Builder {
        return DB::table('steam_users')
            ->select([
                'steam_user_id',
                'steamid',
                'personaname',
                'profileurl',
                'avatarfull',
                'updated'
            ]);
    }

    public static function getOutdatedIdsQuery(LeaderboardSources $leaderboard_source): Builder {
        $thirty_days_ago = new DateTime('-30 day');

        return DB::table(static::getSchemaTableName($leaderboard_source))->select([
            'id',
            'external_id'
        ])->where('updated', '<', $thirty_days_ago->format('Y-m-d H:i:s'))
        ->limit(100000);
    }

    public static function getIdsBySearchTerm(LeaderboardSources $leaderboard_source, string $search_term): object {
        $term_hash_name = sha1($search_term);

        return Cache::store('opcache')->remember("{$leaderboard_source->name}:players:search:{$term_hash_name}", 5, function() use(
            $leaderboard_source,
            $search_term
        ) {
            return DB::table(static::getSchemaTableName($leaderboard_source))->select([
                'id'
            ])
            ->whereRaw('username_search_index @@ to_tsquery(?)', [
                $search_term
            ])
            ->pluck('id', 'id');
        });
    }

    public static function getCacheQuery(LeaderboardSources $leaderboard_source): Builder {
        $query = DB::table(static::getSchemaTableName($leaderboard_source) . ' AS p')
            ->select([
                'p.id'
            ])
            ->leftJoin("user_{$leaderboard_source->name}_player AS up", 'up.player_id', '=', 'p.id')
            ->leftJoin('users AS u', 'u.id', '=', 'up.user_id')
            ->orderBy('p.username', 'asc');

        ExternalSites::addSiteIdSelectFields($query);

        return $query;
    }

    public static function getApiReadQuery(LeaderboardSources $leaderboard_source): Builder {
        $query = DB::table(static::getSchemaTableName($leaderboard_source) . ' AS p');

        static::addSelects($query);
        static::addLeftJoins($leaderboard_source, $query);

        $query->orderBy('p.username', 'asc');

        return $query;
    }
}
