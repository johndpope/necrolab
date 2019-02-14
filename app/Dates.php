<?php

namespace App;

use DateTime;
use DateInterval;
use DatePeriod;
use InvalidArgumentException;
use ElcoBvg\Opcache\Builder;
use ElcoBvg\Opcache\Model;
use Illuminate\Support\Collection;
use App\Traits\GetByName;
use App\Traits\StoredInCache;

class Dates extends Model {
    use GetByName, StoredInCache;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'dates';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];
    
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    
    protected static function getStoredInCacheQuery(): Builder {
        return static::orderBy('name', 'asc');
    }
    
    protected static function getCacheTtl(): DateTime {
        return new DateTime('+1 day');
    }
    
    public static function getValid(DateTime $start_date, DateTime $end_date): Collection {
        $inclusive_end_date = clone $end_date;
        
        $inclusive_end_date->modify('+1 day');
    
        $date_period = new DatePeriod(
            $start_date,
            new DateInterval('P1D'),
            $inclusive_end_date
        );
        
        $dates_count = iterator_count($date_period);
    
        $records = static::whereBetween('name', [
            $start_date->format('Y-m-d'),
            $end_date->format('Y-m-d')
        ])
            ->orderBy('name', 'asc')
            ->get();
        
        if(count($records) != $dates_count) {        
            throw new InvalidArgumentException("One or more date records are missing for the date range of '{$start_date->format('Y-m-d')}' and '{$end_date->format('Y-m-d')}'");
        }
        
        return $records;
    }
}
