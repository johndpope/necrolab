<?php

namespace App;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Traits\HasPartitions;
use App\Traits\HasTempTable;

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
    
    public static function serializeCharacters(array $entry, array $characters) {
        $character_data = [];
    
        if(!empty($characters)) {
            foreach($characters as $character) {
                $rank_name = "{$character->name}_rank";
                
                if(isset($entry[$rank_name])) {                
                    $score_rank = "{$character->name}_score_rank";
                    
                    if(isset($entry[$score_rank])) {
                        $character_data[$character->name]['score']['rank'] = (int)$entry[$score_rank];
                        $character_data[$character->name]['score']['pb_id'] = (int)$entry["{$character->name}_score_pb_id"];
                    }
                    
                    $speed_rank = "{$character->name}_speed_rank";
                    
                    if(isset($entry[$speed_rank])) {
                        $character_data[$character->name]['speed']['rank'] = (int)$entry[$speed_rank];
                        $character_data[$character->name]['speed']['pb_id'] = (int)$entry["{$character->name}_speed_pb_id"];
                    }
                    
                    $deathless_rank = "{$character->name}_deathless_rank";
                    
                    if(isset($entry[$deathless_rank])) {
                        $character_data[$character->name]['deathless']['rank'] = (int)$entry[$deathless_rank];
                        $character_data[$character->name]['deathless']['pb_id'] = (int)$entry["{$character->name}_deathless_pb_id"];
                    }
                    
                    $character_data[$character->name]['rank'] = (int)$entry[$rank_name];
                }
            }
        }
        
        return json_encode($character_data);
    }
    
    public static function createTemporaryTable() {
        DB::statement("
            CREATE TEMPORARY TABLE " . static::getTempTableName() . " (
                power_ranking_id integer,
                steam_user_id integer,
                characters jsonb,
                score_rank integer,
                deathless_rank integer,
                speed_rank integer,
                rank integer
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
                characters,
                score_rank,
                deathless_rank,
                speed_rank,
                rank
            )
            SELECT 
                power_ranking_id,
                steam_user_id,
                characters,
                score_rank,
                deathless_rank,
                speed_rank,
                rank
            FROM " . static::getTempTableName() . "
        ");
    }  
}