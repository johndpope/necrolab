<?php

namespace App;

use DateTime;
use PDO;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use App\Components\Encoder;
use App\ExternalSites;
use App\Traits\HasPartitions;
use App\Traits\HasTempTable;
use App\Releases;
use App\Players;
use App\LeaderboardTypes;

class PowerRankingEntries extends Model {
    use HasPartitions, HasTempTable;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'power_ranking_entries';
    
    /**
     * The primary key associated with the model.
     *
     * @var string
     */
    protected $primaryKey = [
        'power_ranking_id',
        'steam_user_id'
    ];
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    
    public static function getTempInsertQueueBindFlags() {
        return [
            PDO::PARAM_INT,
            PDO::PARAM_INT,
            PDO::PARAM_LOB,
            PDO::PARAM_INT,
            PDO::PARAM_INT,
            PDO::PARAM_INT,
            PDO::PARAM_INT
        ];
    }
    
    public static function serializeCharacters(array $entry, array $characters) {
        $character_data = [];
    
        if(!empty($characters)) {
            foreach($characters as $character) {
                $rank_name = "{$character->name}_rank";
                
                if(isset($entry[$rank_name])) {                
                    $score_rank = "{$character->name}_score_rank";
                    
                    if(isset($entry[$score_rank])) {
                        $character_data[$character->name]['score']['pb_id'] = (int)$entry["{$character->name}_score_pb_id"];
                        $character_data[$character->name]['score']['rank'] = (int)$entry[$score_rank];
                        $character_data[$character->name]['score']['score'] = (int)$entry["{$character->name}_score"];
                    }
                    
                    $speed_rank = "{$character->name}_speed_rank";
                    
                    if(isset($entry[$speed_rank])) {
                        $character_data[$character->name]['speed']['pb_id'] = (int)$entry["{$character->name}_speed_pb_id"];
                        $character_data[$character->name]['speed']['rank'] = (int)$entry[$speed_rank];
                        $character_data[$character->name]['speed']['time'] = (float)$entry["{$character->name}_time"];
                    }
                    
                    $deathless_rank = "{$character->name}_deathless_rank";
                    
                    if(isset($entry[$deathless_rank])) {
                        $character_data[$character->name]['deathless']['pb_id'] = (int)$entry["{$character->name}_deathless_pb_id"];
                        $character_data[$character->name]['deathless']['rank'] = (int)$entry[$deathless_rank];
                        $character_data[$character->name]['deathless']['win_count'] = (int)$entry["{$character->name}_win_count"];
                    }
                    
                    $character_data[$character->name]['rank'] = (int)$entry[$rank_name];
                }
            }
        }
        
        return Encoder::encode($character_data);
    }
    
    public static function createTemporaryTable() {
        DB::statement("
            CREATE TEMPORARY TABLE " . static::getTempTableName() . " (
                power_ranking_id integer,
                steam_user_id integer,
                score_rank integer,
                deathless_rank integer,
                speed_rank integer,
                rank integer,
                characters bytea
            )
            ON COMMIT DROP;
        ");
    }
    
    public static function clear(DateTime $date) {    
        DB::delete("
            DELETE FROM " . static::getTableName($date) . " pre
            USING  power_rankings pr
            WHERE  pre.power_ranking_id = pr.power_ranking_id
            AND    pr.date = :date
        ", [
            ':date' => $date->format('Y-m-d')
        ]);
    }
    
    public static function saveTemp(DateTime $date) {
        DB::statement("
            INSERT INTO " . static::getTableName($date) . " (
                power_ranking_id,
                steam_user_id,
                score_rank,
                deathless_rank,
                speed_rank,
                rank,
                characters
            )
            SELECT 
                power_ranking_id,
                steam_user_id,
                score_rank,
                deathless_rank,
                speed_rank,
                rank,
                characters
            FROM " . static::getTempTableName() . "
        ");
    }
    
    public static function getCacheQuery(DateTime $date) {
        $entries_table_name = static::getTableName($date);

        $query = DB::table('power_rankings AS pr')
            ->select([
                'pr.date',
                'pr.release_id',
                'pr.mode_id',
                'pr.seeded_type_id',
                'pre.steam_user_id',
                'pre.rank',
                'pre.score_rank',
                'pre.deathless_rank',
                'pre.speed_rank',
                'pre.characters'
            ])
            ->join("{$entries_table_name} AS pre", 'pre.power_ranking_id', '=', 'pr.power_ranking_id')
            ->join('steam_users AS su', 'su.steam_user_id', '=', 'pre.steam_user_id')
            ->leftJoin('users AS u', 'u.steam_user_id', '=', 'su.steam_user_id')
            ->where('pr.date', $date->format('Y-m-d'));
            
        ExternalSites::addSiteIdSelectFields($query);
        
        return $query;
    }
    
    public static function getStatsReadQuery(DateTime $date) {
        $entries_table_name = static::getTableName($date);

        $query = DB::table("{$entries_table_name} AS pre")
            ->select([
                'pre.power_ranking_id',
                'pre.rank',
                'pre.score_rank',
                'pre.deathless_rank',
                'pre.speed_rank',
                'pre.characters'
            ])
            ->join("power_rankings AS pr", 'pr.power_ranking_id', '=', 'pre.power_ranking_id')
            ->where('pr.date', $date->format('Y-m-d'));
        
        return $query;
    }
    
    public static function getApiReadQuery(int $release_id, int $mode_id, int $seeded_type_id, DateTime $date) {
        $entries_table_name = static::getTableName($date);
    
        $query = DB::table('power_rankings AS pr')
            ->select([
                'pre.rank',
                'pre.score_rank',
                'pre.deathless_rank',
                'pre.speed_rank',
                'pre.characters'
            ])
            ->join("{$entries_table_name} AS pre", 'pre.power_ranking_id', '=', 'pr.power_ranking_id')
            ->join('steam_users AS su', 'su.steam_user_id', '=', 'pre.steam_user_id')
            ->where('pr.date', $date->format('Y-m-d'))
            ->where('pr.release_id', $release_id)
            ->where('pr.mode_id', $mode_id)
            ->where('pr.seeded_type_id', $seeded_type_id);
        
        Players::addSelects($query);
        Players::addLeftJoins($query);
        
        return $query;
    }
    
    public static function getPlayerApiReadQuery(string $steamid, int $leaderboard_type_id, int $release_id, int $mode_id,  int $seeded_type_id, callable $additional_criteria = NULL) {
        $release = Releases::getById($release_id);
        
        $start_date = new DateTime($release['start_date']);
        $end_date = new DateTime($release['end_date']);
    
        $query = NULL;
        
        $table_names = static::getTableNames($start_date, $end_date);
        
        if(!empty($table_names)) {
            foreach($table_names as $table_name) {                    
                $partition_query = DB::table('power_rankings AS pr')
                    ->select([
                        'pr.date',
                        'pre.rank',
                        'pre.score_rank',
                        'pre.deathless_rank',
                        'pre.speed_rank',
                        'pre.characters'
                    ])
                    ->join("{$table_name} AS pre", 'pre.power_ranking_id', '=', 'pr.power_ranking_id')
                    ->join('steam_users AS su', 'su.steam_user_id', '=', 'pre.steam_user_id')
                    ->where('su.steamid', $steamid)
                    ->whereBetween('pr.date', [
                        $start_date->format('Y-m-d'),
                        $end_date->format('Y-m-d')
                    ])
                    ->where('pr.release_id', $release_id)
                    ->where('pr.mode_id', $mode_id)
                    ->where('pr.seeded_type_id', $seeded_type_id);
                
                if(!empty($additional_criteria)) {
                    call_user_func_array($additional_criteria, [
                        $partition_query
                    ]);
                }
                
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
    
    public static function getPlayerCategoryApiReadQuery(string $steamid, LeaderboardTypes $leaderboard_type, int $release_id, int $mode_id,  int $seeded_type_id) { 
        $additional_criteria = function(Builder $query) use ($leaderboard_type) {
            $rank_name = "{$leaderboard_type->name}_rank";
        
            $query->whereNotNull("pre.{$rank_name}");
        };
    
        return static::getPlayerApiReadQuery($steamid, $release_id, $mode_id, $seeded_type_id, $additional_criteria);
    }
}
