<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Traits\GetByName;
use App\Traits\GetById;
use App\Traits\HasManualSequence;
use App\Traits\HasTempTable;

class RunResults extends Model {
    use GetByName, GetById, HasManualSequence, HasTempTable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'run_results';
    
    /**
     * The primary key associated with the model.
     *
     * @var string
     */
    protected $primaryKey = 'run_result_id';
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    
    public static function createTemporaryTable() {
        DB::statement("
            CREATE TEMPORARY TABLE " . static::getTempTableName() . " (
                run_result_id smallint,
                name character varying(255),
                is_win smallint
            )
            ON COMMIT DROP;
        ");
    }
    
    public static function saveNewFromTemp() {    
        DB::statement("
            INSERT INTO run_results (
                run_result_id,
                name,
                is_win
            )
            SELECT 
                run_result_id,
                name,
                is_win
            FROM " . static::getTempTableName() . "
        ");
    }
}
