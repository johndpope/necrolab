<?php
namespace App\Components;

use Exception;
use stdClass;
use Illuminate\Http\Request;
use App\Components\CacheNames\Prefix;

class RequestModels {
    protected static $models = [
        'leaderboard_source' => '\App\LeaderboardSources',
        'date' => '\App\Dates',
        'leaderboard_type' => '\App\LeaderboardTypes',
        'character' => '\App\Characters',
        'release' => '\App\Releases',
        'mode' => '\App\Modes',
        'seeded_type' => '\App\SeededTypes',
        'multiplayer_type' => '\App\MultiplayerTypes',
        'soundtrack' => '\App\Soundtracks',
        'number_of_days' => '\App\DailyRankingDayTypes'
    ];

    protected $request;
    
    protected $fields = [];
    
    protected $loaded = [];

    public function __construct(Request $request, array $fields) {
        $this->request = $request;
        
        $this->fields = $fields;
        
        $this->load();
    }
    
    protected function load(): void {
        if(empty($this->fields)) {
            throw new Exception('No fields were specified to load from the request.');
        }
        
        foreach($this->fields as $field) {
            if(!isset(static::$models[$field])) {
                throw new Exception("Field '{$field}' is not supported.");
            }
            
            $model_class = static::$models[$field];
            
            if(isset($this->request->$field)) {
                $this->loaded[$field] = $model_class::getByName($this->request->$field);
            }
            else {
                $this->loaded[$field] = $model_class::getDefaultRecord();
            }
        }
    }
    
    public function getCacheNamePrefix(): Prefix {
        $prefix = new Prefix();
        
        foreach($this->loaded as $name => $value) {
            $prefix->$name = $value->id;
        }
        
        return $prefix;
    }
    
    public function __get(string $name): object {
        if(!isset($this->loaded[$name])) {
            throw new Exception("Field '{$name}' is not a valid loaded model.");
        }
        
        return $this->loaded[$name];
    }
    
    public function __isset(string $name): bool {
        return isset($this->loaded[$name]);
    }
}
