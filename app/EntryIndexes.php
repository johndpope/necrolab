<?php

namespace App;

use PDO;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
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
}