<?php

namespace App;

use stdClass;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Traits\GeneratesNewInstance;
use App\Traits\IsSchemaTable;
use App\Traits\SchemaGetByName;
use App\Traits\GetById;
use App\Traits\HasManualSequence;
use App\Traits\HasTempTable;
use App\Traits\CanBeVacuumed;
use App\LeaderboardSources;

class Seeds extends Model {
    use GeneratesNewInstance, IsSchemaTable, SchemaGetByName, GetById, HasManualSequence, HasTempTable, CanBeVacuumed;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'seeds';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /*
        This functionality was borrowed from: https://braceyourselfgames.com/forums/viewtopic.php?f=5&t=3240

        All credit goes to blueblimp. Thank you!
    */
    public static function getSeedFromZ1Seed(int $zone_1_seed): int {
        $base = 6;
        $mul = 23987;
        $invmul = 492935547;
        $period = 2 ** 32;
        $overflow = 2 ** 32;

        if((($mul * $invmul) % $period) != 1) {
            throw new Exception("((mul * invmul) % period) does not equal one.");
        }

        $zone_1_seed -= $base;

        while($zone_1_seed < 0) {
            $zone_1_seed += $period;
        }

        $seed = ($zone_1_seed * $invmul) % $period;

        if($seed >= $overflow) {
            $seed -= $period;
        }

        return $seed;
    }

    public static function getOldDLCSeedFromZ1Seed(int $zone_1_seed): int {
        $multiplied_seed = $zone_1_seed * 1899818559;

        $seed = $multiplied_seed % 2147483647;

        return $seed;
    }

    /*
        This functionality was borrowed from: https://github.com/necrommunity/replay-parser/blob/master/js/main.js#L5

        All credit goes to AlexisYJ and Grimy. Thank you!
    */
    public static function getDLCSeedFromZ1Seed(int $zone_1_seed): int {
        $added_seed = $zone_1_seed + 1073765959;

        $multiplied_seed = $added_seed * 225371434;

        $modulus_seed = $multiplied_seed % 2147483647;

        $seed = $modulus_seed % 1899818559;

        return $seed;
    }

    public static function generateNew(LeaderboardSources $leaderboard_source): int {
        $new_seed = NULL;

        do {
            $random_seed = random_int(0, PHP_INT_MAX);

            $existing_record = DB::table(static::getSchemaTableName($leaderboard_source))->where('name', $random_seed)->first();

            if(empty($existing_record)) {
                //TODO: Adjust this to work with the new schema structure
                $new_record = new static();

                $new_record->id = static::getNewRecordId($leaderboard_source);
                $new_record->name = $random_seed;

                $new_record->save();

                $new_seed = $random_seed;
            }
        }
        while(!isset($new_seed));

        return $new_seed;
    }

    public static function createTemporaryTable(LeaderboardSources $leaderboard_source): void {
        DB::statement("
            CREATE TEMPORARY TABLE " . static::getTempTableName($leaderboard_source) . " (
                id bigint,
                name character varying(255)
            )
            ON COMMIT DROP;
        ");
    }

    public static function saveNewTemp(LeaderboardSources $leaderboard_source): void {
        DB::statement("
            INSERT INTO " . static::getSchemaTableName($leaderboard_source) . " (
                id,
                name
            )
            SELECT 
                id,
                name
            FROM " . static::getTempTableName($leaderboard_source) . "
        ");
    }

    public static function updateFromTemp(): void {}
}
