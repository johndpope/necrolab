<?php

namespace App;

use DateTime;
use DateInterval;
use stdClass;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use App\Traits\IsSchemaTable;
use App\Traits\HasTempTable;
use App\Traits\HasManualSequence;
use App\Traits\AddsSqlCriteria;
use App\Components\PostgresCursor;
use App\Dates;
use App\Characters;
use App\Releases;
use App\Modes;
use App\LeaderboardSources;
use App\LeaderboardTypes;
use App\LeaderboardSnapshots;
use App\SeededTypes;
use App\Soundtracks;
use App\MultiplayerTypes;
use App\DailyDateFormats;
use App\LeaderboardRankingTypes;
use App\PlayerPbs;
use App\Players;

class Leaderboards extends Model {
    use IsSchemaTable, HasTempTable, HasManualSequence, AddsSqlCriteria;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'leaderboards';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    
    public static function getSeededFlags(): array {
        return [
            0, 
            1
        ];
    }
    
    public static function setPropertiesFromName(LeaderboardSources $leaderboard_source, object $leaderboard, ?DailyDateFormats $daily_date_format): void {
        if(!isset($leaderboard->name)) {
            throw new Exception('name property in specified leaderboard object is required but not found.');
        }
        
        $leaderboard->leaderboard_type = LeaderboardTypes::getMatchFromString($leaderboard_source, $leaderboard->name);
        
        $leaderboard->character = Characters::getMatchFromString($leaderboard_source, $leaderboard->name);

        $leaderboard->release = Releases::getMatchFromString($leaderboard_source, $leaderboard->name);
        
        $leaderboard->mode = Modes::getMatchFromString($leaderboard_source, $leaderboard->name);
        
        $leaderboard->seeded_type = SeededTypes::getMatchFromString($leaderboard_source, $leaderboard->name);
        
        $leaderboard->multiplayer_type = MultiplayerTypes::getMatchFromString($leaderboard_source, $leaderboard->name);
        
        $leaderboard->soundtrack = Soundtracks::getMatchFromString($leaderboard_source, $leaderboard->name);
        
        $leaderboard->daily_date = NULL;
        
        if(!empty($daily_date_format)) {
            $unformatted_daily_date = preg_replace("/{$daily_date_format->clean_regex}/", "", $leaderboard->name);
            
            if(!empty($unformatted_daily_date)) {            
                $leaderboard->leaderboard_type = LeaderboardTypes::getByName('daily');
                
                $daily_date = DateTime::createFromFormat($daily_date_format->format, $unformatted_daily_date);

                $last_errors = DateTime::getLastErrors();
                
                if(!(empty($last_errors['warning_count']) && empty($last_errors['error_count']))) {
                    $daily_date = NULL;
                }
                
                if(!empty($daily_date)) {
                    $date_formatted = $daily_date->format('Y-m-d');
                
                    $leaderboard->daily_date = Dates::getByName($date_formatted);
                    
                    // Dailies should always be seeded
                    $leaderboard->seeded_type = SeededTypes::getByName('seeded');
                    
                    if(empty($leaderboard->daily_date)) {
                        throw new Exception("Date '{$date_formatted}' does not exist in the dates table.");
                    }
                }
            }
        }
        
        $leaderboard->ranking_types = [];

        //TODO: Replace with dynamic system
        if(
            $leaderboard->leaderboard_type->name != 'daily' && 
            $leaderboard->soundtrack->name == 'default' &&
            $leaderboard->multiplayer_type->name == 'single'
        ) {
            $leaderboard->ranking_types[] = RankingTypes::getByName('power');
            $leaderboard->ranking_types[] = RankingTypes::getByName('super');
        }
        
        if(
            $leaderboard->leaderboard_type->name == 'daily' && 
            $leaderboard->character->name == 'cadence' &&
            !empty($daily_date) &&
            $leaderboard->soundtrack->name == 'default' && 
            $leaderboard->multiplayer_type->name == 'single' && 
            $leaderboard->seeded_type->name == 'seeded'
        ) {
            $leaderboard->ranking_types[] = RankingTypes::getByName('daily');
        }
    }
    
    public static function isValid(LeaderboardSources $leaderboard_source, object $leaderboard, DateTime $date): bool {        
        //$blacklist_record = Blacklist::getRecordById($leaderboard->external_id);
        
        $date_within_release = false;
        
        $start_date = new DateTime($leaderboard->release->start_date);
        $end_date = new DateTime($leaderboard->release->end_date);
        
        if($date >= $start_date && $date <= $end_date) {
            $date_within_release = true;
        }
        
        $is_valid = false;
        
        if(
            //empty($blacklist_record) && 
            !empty($leaderboard->character->id) && 
            $date_within_release 
        ) {
            $is_valid = true;
        }
    
        return $is_valid;
    }
    
    public static function createTemporaryTable(LeaderboardSources $leaderboard_source): void {
        DB::statement("
            CREATE TEMPORARY TABLE " . static::getTempTableName($leaderboard_source) . " (
                id integer,
                leaderboard_type_id smallint,
                character_id smallint,
                release_id smallint,
                mode_id smallint,
                seeded_type_id smallint,
                multiplayer_type_id smallint,
                soundtrack_id smallint,
                daily_date_id smallint,
                external_id character varying(255),
                name character varying(255),
                display_name character varying(255),
                url text
            )
            ON COMMIT DROP;
        ");
    }
    
    public static function saveNewTemp(LeaderboardSources $leaderboard_source): void {
        DB::statement("
            INSERT INTO " . static::getSchemaTableName($leaderboard_source) . " (
                id,
                leaderboard_type_id,
                character_id,
                release_id,
                mode_id,
                seeded_type_id,
                multiplayer_type_id,
                soundtrack_id,
                daily_date_id,
                external_id,
                name,
                display_name,
                url
            )
            SELECT 
                id,
                leaderboard_type_id,
                character_id,
                release_id,
                mode_id,
                seeded_type_id,
                multiplayer_type_id,
                soundtrack_id,
                daily_date_id,
                external_id,
                name,
                display_name,
                url
            FROM " . static::getTempTableName($leaderboard_source) . "
        ");
    }
    
    public static function updateFromTemp(): void {}
    
    public static function getIdsByExternalId(LeaderboardSources $leaderboard_source): array {
        $query = DB::table(static::getSchemaTableName($leaderboard_source))->select([
            'external_id',
            'id'
        ]);
        
        $cursor = new PostgresCursor(
            "{$leaderboard_source->name}_leaderboards_by_external_id", 
            $query,
            5000
        );
        
        $ids_by_external_id = [];
        
        foreach($cursor->getRecord() as $leaderboard) {
            $ids_by_external_id[$leaderboard->external_id] = $leaderboard->id;
        }
        
        return $ids_by_external_id;
    }
    
    public static function getLegacyImportQuery(): Builder {
        return DB::table('leaderboards AS l')
            ->select([
                'l.leaderboard_id',
                'l.name',
                'l.url',
                'l.lbid',
                'l.display_name',
                'l.is_speedrun',
                'l.is_custom',
                'l.is_co_op',
                'l.is_seeded',
                'l.is_daily',
                'l.daily_date',
                'l.is_score_run',
                'l.is_deathless',
                'l.is_dev',
                'l.is_prod',
                'l.is_power_ranking',
                'l.is_daily_ranking',
                'c.name AS character',
                'r.name AS release',
                'm.name AS mode'
            ])
            ->join('characters AS c', 'c.character_id', '=', 'l.character_id')
            ->join('releases AS r', 'r.release_id', '=', 'l.release_id')
            ->join('modes AS m', 'm.mode_id', '=', 'l.mode_id');
    }
    
    public static function getApiReadQuery(LeaderboardSources $leaderboard_source): Builder {
        $leaderboard_ranking_types_query = DB::table(LeaderboardRankingTypes::getSchemaTableName($leaderboard_source) . ' AS lrt')
            ->select([
                'lrt.leaderboard_id',
                DB::raw('string_agg(rt.name, \',\' ORDER BY rt.id) AS ranking_types')
            ])
            ->join('ranking_types AS rt', 'rt.id', '=', 'lrt.ranking_type_id')
            ->groupBy('lrt.leaderboard_id');
    
        return DB::table(static::getSchemaTableName($leaderboard_source) . ' AS l')
            ->select([
                'l.id',
                'l.external_id',
                'l.name',
                'l.display_name',
                'leaderboard_ranking_types.ranking_types',
                'lt.name AS leaderboard_type',
                'c.name AS character',
                'r.name AS release',
                'm.name AS mode',
                'st.name AS seeded_type',
                'mt.name AS multiplayer_type',
                'strk.name AS soundtrack',
                'lt.show_seed',
                'lt.show_replay',
                'lt.show_zone_level'
            ])
            ->join('leaderboard_types AS lt', 'lt.id', '=', 'l.leaderboard_type_id')
            ->join('releases AS r', 'r.id', '=', 'l.release_id')
            ->join('modes AS m', 'm.id', '=', 'l.mode_id')
            ->join('characters AS c', 'c.id', '=', 'l.character_id')
            ->join('seeded_types AS st', 'st.id', '=', 'l.seeded_type_id')
            ->join('multiplayer_types AS mt', 'mt.id', '=', 'l.multiplayer_type_id')
            ->join('soundtracks AS strk', 'strk.id', '=', 'l.soundtrack_id')
            ->leftJoinSub($leaderboard_ranking_types_query, 'leaderboard_ranking_types', function($join) {
                $join->on('leaderboard_ranking_types.leaderboard_id', '=', 'l.id');
            })
            ->orderBy('lt.name', 'asc')
            ->orderBy('l.name', 'asc');
    }
    
    public static function getNonDailyApiReadQuery(
        LeaderboardSources $leaderboard_source, 
        Characters $character,
        Releases $release, 
        Modes $mode
    ): Builder {
        return static::getApiReadQuery($leaderboard_source)
            ->where('lt.name', '!=', 'daily')
            ->where('l.character_id', $character->id)
            ->where('l.release_id', $release->id)
            ->where('l.mode_id', $mode->id);
    }
    
    public static function getCategoryApiReadQuery(
        LeaderboardSources $leaderboard_source, 
        LeaderboardTypes $leaderboard_type,
        Characters $character,
        Releases $release,
        Modes $mode
    ): Builder {
        return static::getApiReadQuery($leaderboard_source)
            ->where('l.leaderboard_type_id', $leaderboard_type->id)
            ->where('l.character_id', $character->id)
            ->where('l.release_id', $release->id)
            ->where('l.mode_id', $mode->id);
    }
    
    public static function getCharactersApiReadQuery(
        LeaderboardSources $leaderboard_source, 
        LeaderboardTypes $leaderboard_type,
        Releases $release,
        Modes $mode,
        SeededTypes $seeded_type,
        MultiplayerTypes $multiplayer_type,
        Soundtracks $soundtrack
    ): Builder {
        $query = static::getApiReadQuery($leaderboard_source);
    
        $query->orders = [];
        
        $query
            ->where('l.leaderboard_type_id', $leaderboard_type->id)
            ->where('l.release_id', $release->id)
            ->where('l.mode_id', $mode->id)
            ->where('l.seeded_type_id', $seeded_type->id)
            ->where('l.multiplayer_type_id', $multiplayer_type->id)
            ->where('l.soundtrack_id', $soundtrack->id)
            ->orderBy('c.sort_order', 'asc');
    
        return $query;
    }
    
    public static function getApiShowQuery(LeaderboardSources $leaderboard_source, string $leaderboard_id): Builder {
        return static::getApiReadQuery($leaderboard_source)
            ->where('l.external_id', $leaderboard_id);
    }
    
    public static function getApiByAttributesQuery(
        LeaderboardSources $leaderboard_source, 
        LeaderboardTypes $leaderboard_type,
        Characters $character,
        Releases $release,
        Modes $mode,
        SeededTypes $seeded_type, 
        MultiplayerTypes $multiplayer_type,
        Soundtracks $soundtrack
    ): Builder {
        return static::getApiReadQuery($leaderboard_source)
            ->where('l.leaderboard_type_id', $leaderboard_type->id)
            ->where('l.character_id', $character->id)
            ->where('l.release_id', $release->id)
            ->where('l.mode_id', $mode->id)
            ->where('l.seeded_type_id', $seeded_type->id)
            ->where('l.multiplayer_type_id', $multiplayer_type->id)
            ->where('l.soundtrack_id', $soundtrack->id);
    }
    
    public static function getDailyApiReadQuery(
        LeaderboardSources $leaderboard_source, 
        Characters $character,
        Releases $release,
        Modes $mode,
        MultiplayerTypes $multiplayer_type,
        Soundtracks $soundtrack
    ): Builder {    
        return DB::table(static::getSchemaTableName($leaderboard_source) . ' AS l')
            ->select([
                'd.name AS daily_date',
                'ls.players',
                'ls.details'
            ])
            ->join('dates AS d', 'd.id', '=', 'l.daily_date_id')
            ->join('leaderboard_types AS lt', 'lt.id', '=', 'l.leaderboard_type_id')
            ->join(LeaderboardSnapshots::getSchemaTableName($leaderboard_source) . ' AS ls', function($join) {
                $join->on('ls.leaderboard_id', '=', 'l.id')
                    ->on('ls.date_id', '=', 'l.daily_date_id');
            })
            ->where('lt.name', 'daily')
            ->where('l.character_id', $character->id)
            ->where('l.release_id', $release->id)
            ->where('l.mode_id', $mode->id)
            ->where('l.multiplayer_type_id', $multiplayer_type->id)
            ->where('l.soundtrack_id', $soundtrack->id)
            ->orderBy('d.name', 'desc');
    }
    
    public static function getPlayerApiReadQuery(LeaderboardSources $leaderboard_source, string $player_id): Builder {
        $leaderboard_ranking_types_query = DB::table(LeaderboardRankingTypes::getSchemaTableName($leaderboard_source) . ' AS lrt')
            ->select([
                'lrt.leaderboard_id',
                DB::raw('string_agg(rt.name, \',\' ORDER BY rt.id) AS ranking_types')
            ])
            ->join('ranking_types AS rt', 'rt.id', '=', 'lrt.ranking_type_id')
            ->groupBy('lrt.leaderboard_id');
    
        return DB::table(PlayerPbs::getSchemaTableName($leaderboard_source) . ' AS ppb')
            ->select([
                'l.id',
                'l.external_id',
                'l.name',
                'l.display_name',
                'leaderboard_ranking_types.ranking_types',
                'lt.name AS leaderboard_type',
                'c.name AS character',
                'r.name AS release',
                'm.name AS mode',
                'st.name AS seeded_type',
                'mt.name AS multiplayer_type',
                'strk.name AS soundtrack',
                'lt.show_seed',
                'lt.show_replay',
                'lt.show_zone_level'
            ])
            ->join(Players::getSchemaTableName($leaderboard_source) . ' AS p', 'p.id', '=', 'ppb.player_id')
            ->join(static::getSchemaTableName($leaderboard_source) . ' AS l', 'l.id', '=', 'ppb.leaderboard_id')
            ->join('leaderboard_types AS lt', 'lt.id', '=', 'l.leaderboard_type_id')
            ->join('characters AS c', 'c.id', '=', 'l.character_id')
            ->join('releases AS r', 'r.id', '=', 'l.release_id')
            ->join('modes AS m', 'm.id', '=', 'l.mode_id')
            ->join('seeded_types AS st', 'st.id', '=', 'l.seeded_type_id')
            ->join('multiplayer_types AS mt', 'mt.id', '=', 'l.multiplayer_type_id')
            ->join('soundtracks AS strk', 'strk.id', '=', 'l.soundtrack_id')
            ->leftJoinSub($leaderboard_ranking_types_query, 'leaderboard_ranking_types', function($join) {
                $join->on('leaderboard_ranking_types.leaderboard_id', '=', 'l.id');
            })
            ->where('p.external_id', $player_id)
            ->orderBy('lt.name', 'asc')
            ->orderBy('l.name', 'asc');
    }
    
    public static function getPlayerNonDailyApiReadQuery(
        LeaderboardSources $leaderboard_source,
        string $player_id,
        Characters $character,
        Releases $release,
        Modes $mode
    ): Builder {
        return static::getPlayerApiReadQuery($leaderboard_source, $player_id)
            ->where('lt.name', '!=', 'daily')
            ->where('l.character_id', $character->id)
            ->where('l.release_id', $release->id)
            ->where('l.mode_id', $mode->id);
    }
    
    public static function getPlayerCategoryApiReadQuery(
        LeaderboardSources $leaderboard_source,
        string $player_id,
        LeaderboardTypes $leaderboard_type,
        Characters $character,
        Releases $release,
        Modes $mode
    ): Builder {
        return static::getPlayerApiReadQuery($leaderboard_source, $player_id)
            ->where('l.leaderboard_type_id', $leaderboard_type->id)
            ->where('l.character_id', $character->id)
            ->where('l.release_id', $release->id)
            ->where('l.mode_id', $mode->id);
    }
    
    public static function getPlayerDailyApiReadQuery(
        LeaderboardSources $leaderboard_source, 
        string $player_id,
        Characters $character,
        Releases $release,
        Modes $mode,
        MultiplayerTypes $multiplayer_type,
        Soundtracks $soundtrack
    ): Builder {
        return DB::table(PlayerPbs::getSchemaTableName($leaderboard_source) . ' AS ppb')
            ->select([
                'l.id',
                'd.name AS daily_date',
                'ppb.details'
            ])
            ->join(Players::getSchemaTableName($leaderboard_source) . ' AS p', 'p.id', '=', 'ppb.player_id')
            ->join(static::getSchemaTableName($leaderboard_source) . ' AS l', 'l.id', '=', 'ppb.leaderboard_id')
            ->join('dates AS d', 'd.id', '=', 'l.daily_date_id')
            ->join('leaderboard_types AS lt', 'lt.id', '=', 'l.leaderboard_type_id')
            ->where('p.external_id', $player_id)
            ->where('lt.name', 'daily')
            ->where('l.character_id', $character->id)
            ->where('l.release_id', $release->id)
            ->where('l.mode_id', $mode->id)
            ->where('l.multiplayer_type_id', $multiplayer_type->id)
            ->where('l.soundtrack_id', $soundtrack->id)
            ->orderBy('d.name', 'desc');
    }
}
