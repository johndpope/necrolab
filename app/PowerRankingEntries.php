<?php

namespace App;

use DateTime;
use PDO;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use App\Components\Encoder;
use App\ExternalSites;
use App\Traits\IsSchemaTable;
use App\Traits\HasPartitions;
use App\Traits\HasTempTable;
use App\Players;
use App\LeaderboardSources;
use App\LeaderboardTypes;
use App\Dates;
use App\PowerRankings;
use App\Releases;
use App\Modes;
use App\SeededTypes;
use App\MultiplayerTypes;
use App\Soundtracks;

class PowerRankingEntries extends Model {
    use IsSchemaTable, HasPartitions, HasTempTable;
    
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
        'player_id'
    ];
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    
    public static function getTempInsertQueueBindFlags(): array {
        return [
            PDO::PARAM_INT,
            PDO::PARAM_INT,
            PDO::PARAM_INT,
            PDO::PARAM_LOB,
            PDO::PARAM_LOB
        ];
    }
    
    public static function serializeCharacters(array $entry, array $characters, array $leaderboard_types): string {
        $character_data = [];
    
        if(!empty($characters)) {
            foreach($characters as $character) {
                $rank_name = "{$character->name}_rank";
                
                if(isset($entry[$rank_name])) {
                    if(!empty($leaderboard_types)) {
                        foreach($leaderboard_types as $leaderboard_type) {                            
                            $category_rank_name = "{$character->name}_{$leaderboard_type->name}_rank";
                            
                            if(isset($entry[$category_rank_name])) {
                                $pb_id_name = "{$character->name}_{$leaderboard_type->name}_pb_id";
                            
                                $character_data[$character->name]['categories'][$leaderboard_type->name]['pb_id'] = (int)$entry[$pb_id_name];
                                $character_data[$character->name]['categories'][$leaderboard_type->name]['rank'] = (int)$entry[$category_rank_name];
                                $character_data[$character->name]['categories'][$leaderboard_type->name]['details'] = [];
                                
                                if(!empty($leaderboard_type->details_columns)) {
                                    foreach($leaderboard_type->details_columns as $details_column_name) {
                                        $details_name = "{$character->name}_{$leaderboard_type->name}_{$details_column_name}";
                                    
                                        if(isset($entry[$details_name])) {
                                            $character_data[$character->name]['categories'][$leaderboard_type->name]['details'][$details_column_name] = $entry[$details_name];
                                        }
                                    }
                                }
                            }
                        }
                    }
                    
                    $character_data[$character->name]['rank'] = (int)$entry[$rank_name];
                }
            }
        }
        
        return Encoder::encode($character_data);
    }
    
    public static function createTemporaryTable(LeaderboardSources $leaderboard_source): void {
        DB::statement("
            CREATE TEMPORARY TABLE " . static::getTempTableName($leaderboard_source) . " (
                power_ranking_id integer,
                player_id integer,
                rank integer,
                characters bytea,
                category_ranks bytea
            )
            ON COMMIT DROP;
        ");
    }
    
    public static function clear(LeaderboardSources $leaderboard_source, Dates $date): void {    
        DB::delete("
            DELETE FROM " . static::getTableName($leaderboard_source, new DateTime($date->name)) . " pre
            USING  " . PowerRankings::getSchemaTableName($leaderboard_source) . " pr
            WHERE  pre.power_ranking_id = pr.id
            AND    pr.date_id = :date_id
        ", [
            ':date_id' => $date->id
        ]);
    }
    
    public static function saveNewTemp(LeaderboardSources $leaderboard_source, Dates $date): void {
        DB::statement("
            INSERT INTO " . static::getTableName($leaderboard_source, new DateTime($date->name)) . " (
                power_ranking_id,
                player_id,
                rank,
                characters,
                category_ranks
            )
            SELECT 
                power_ranking_id,
                player_id,
                rank,
                characters,
                category_ranks
            FROM " . static::getTempTableName($leaderboard_source) . "
        ");
    }
    
    public static function updateFromTemp(LeaderboardSources $leaderboard_source) {}
    
    public static function getCacheQuery(LeaderboardSources $leaderboard_source, Dates $date): Builder {
        $entries_table_name = static::getTableName($leaderboard_source, new DateTime($date->name));

        $query = DB::table(PowerRankings::getSchemaTableName($leaderboard_source) . ' AS pr')
            ->select([
                'pr.release_id',
                'pr.mode_id',
                'pr.seeded_type_id',
                'pr.multiplayer_type_id',
                'pr.soundtrack_id',
                'pre.player_id',
                'pre.rank',
                'pre.characters',
                'pre.category_ranks'
            ])
            ->join("{$entries_table_name} AS pre", 'pre.power_ranking_id', '=', 'pr.id')
            ->leftJoin("user_{$leaderboard_source->name}_player AS up", 'up.player_id', '=', 'pre.player_id')
            ->leftJoin('users AS u', 'u.id', '=', 'up.user_id')
            ->where('pr.date_id', $date->id);
            
        ExternalSites::addSiteIdSelectFields($query);
        
        return $query;
    }
    
    public static function getStatsReadQuery(LeaderboardSources $leaderboard_source, Dates $date): Builder {
        $entries_table_name = static::getTableName($leaderboard_source, new DateTime($date->name));

        $query = DB::table(PowerRankings::getSchemaTableName($leaderboard_source) . ' AS pr')
            ->select([
                'pre.power_ranking_id',
                'pre.rank',
                'pre.characters',
                'pre.category_ranks'
            ])
            ->join("{$entries_table_name} AS pre", 'pre.power_ranking_id', '=', 'pr.id')
            ->where('pr.date_id', $date->id);
        
        return $query;
    }
    
    public static function getApiReadQuery(
        LeaderboardSources $leaderboard_source,
        Releases $release,
        Modes $mode,
        SeededTypes $seeded_type,
        MultiplayerTypes $multiplayer_type,
        Soundtracks $soundtrack,
        Dates $date
    ): Builder {
        $entries_table_name = static::getTableName($leaderboard_source, new DateTime($date->name));
    
        $query = DB::table(PowerRankings::getSchemaTableName($leaderboard_source) . ' AS pr')
            ->select([
                'pre.rank',
                'pre.characters',
                'pre.category_ranks'
            ])
            ->join("{$entries_table_name} AS pre", 'pre.power_ranking_id', '=', 'pr.id')
            ->join(Players::getSchemaTableName($leaderboard_source) . ' AS p', 'p.id', '=', 'pre.player_id')
            ->where('pr.release_id', $release->id)
            ->where('pr.mode_id', $mode->id)
            ->where('pr.seeded_type_id', $seeded_type->id)
            ->where('pr.multiplayer_type_id', $multiplayer_type->id)
            ->where('pr.soundtrack_id', $soundtrack->id)
            ->where('pr.date_id', $date->id);
        
        Players::addSelects($query);
        Players::addLeftJoins($leaderboard_source, $query);
        
        return $query;
    }
    
    public static function getPlayerApiReadQuery(
        string $player_id, 
        LeaderboardSources $leaderboard_source,
        Releases $release,
        Modes $mode,
        SeededTypes $seeded_type,
        MultiplayerTypes $multiplayer_type,
        Soundtracks $soundtrack
    ): Builder {        
        $start_date = new DateTime($release['start_date']);
        $end_date = new DateTime($release['end_date']);
    
        $query = NULL;
        
        $table_names = static::getTableNames($leaderboard_source, $start_date, $end_date);
        
        if(!empty($table_names)) {
            foreach($table_names as $table_name) {                    
                $partition_query = DB::table(PowerRankings::getSchemaTableName($leaderboard_source) . ' AS pr')
                    ->select([
                        'd.name AS date',
                        'pre.rank',
                        'pre.characters',
                        'pre.category_ranks'
                    ])
                    ->join("dates AS d", 'd.id', '=', 'pr.date_id')
                    ->join("{$table_name} AS pre", 'pre.power_ranking_id', '=', 'pr.id')
                    ->join(Players::getSchemaTableName($leaderboard_source) . ' AS p', 'p.id', '=', 'pre.player_id')
                    ->where('pr.release_id', $release->id)
                    ->where('pr.mode_id', $mode->id)
                    ->where('pr.seeded_type_id', $seeded_type->id)
                    ->where('pr.multiplayer_type_id', $multiplayer_type->id)
                    ->where('pr.soundtrack_id', $soundtrack->id)
                    ->whereBetween('d.name', [
                        $start_date->format('Y-m-d'),
                        $end_date->format('Y-m-d')
                    ])
                    ->where('p.external_id', $player_id);
                
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
}
