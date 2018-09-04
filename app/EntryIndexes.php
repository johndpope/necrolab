<?php

namespace App;

use PDO;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Components\Encoder;
use App\Components\CacheNames\Users\Steam as SteamUsersCacheNames;
use App\Traits\HasTempTable;

class EntryIndexes extends Model {
    use HasTempTable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'entry_indexes';
    
    /**
     * The primary key associated with the model.
     *
     * @var string
     */
    protected $primaryKey = '';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    
    public static function getTempInsertQueueBindFlags() {
        return [
            PDO::PARAM_LOB,
            PDO::PARAM_STR,
            PDO::PARAM_STR
        ];
    }
    
    public static function createTemporaryTable() {        
        DB::statement("
            CREATE TEMPORARY TABLE " . static::getTempTableName() . " (
                data bytea NOT NULL,
                name character varying(255) NOT NULL,
                sub_name character varying(255) NOT NULL
            )
            ON COMMIT DROP;
        ");
    }
    
    public static function saveTemp() {
        DB::statement("
            INSERT INTO entry_indexes (
                data,
                name,
                sub_name
            )
            SELECT 
                data,
                name,
                sub_name
            FROM " . static::getTempTableName() . "
            ON CONFLICT (name, sub_name) DO 
            UPDATE 
            SET 
                data = excluded.data
        ");
    }
    
    public static function getDecodedRecord(string $name, string $sub_name = '') {
        return Cache::store('opcache')->remember("{$name}:{$sub_name}", 5, function() use($name, $sub_name) {                            
            $encoded_data = static::where('name', $name)
                ->where('sub_name', $sub_name)
                ->first();
            
            $decoded_data = [];
            
            if(!empty($encoded_data)) {
                $decoded_data = Encoder::decode(stream_get_contents($encoded_data->data));
            }

            return $decoded_data;
        });
    }
}
