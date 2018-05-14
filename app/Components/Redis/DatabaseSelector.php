<?php
namespace App\Components\Redis;

use Exception;
use DateTime;
use Illuminate\Redis\Connections\PhpRedisConnection;

class DatabaseSelector {  
    protected $redis;

    public function __construct(PhpRedisConnection $redis, DateTime $date) {        
        $this->redis = $redis;
        
        $this->date = $date;
    }
    
    public function run() {
        $database = 0;
        
        $database_selected = false;
        
        do {
            if($database > 15) {
                throw new Exception("All 16 power ranking redis databases are taken so one cannot be selected for processing '{$this->date->format('Y-m-d')}'.");
            }
            
            $this->redis->select($database);
            
            $processing_for = $this->redis->get('processing_for');
            
            if(empty($processing_for)) {
                $database_selected = true;
            }
            else {
                $began_processing = $this->redis->get('began_processing');
                
                if(!empty($began_processing)) {
                    $began_processing_timestamp = new DateTime($began_processing);
                    
                    $time_since_beginning = $began_processing_timestamp->diff(new DateTime());
                    
                    if($time_since_beginning->format('%a') >= 1) {
                        $database_selected = true;
                    }
                }
            }
            
            if(!$database_selected) {            
                $database += 1;
            }
        }
        while(!$database_selected);
        
        $this->redis->flushDb();
        
        $this->redis->set('processing_for', $this->date->format('Y-m-d'));
        $this->redis->set('began_processing', date('Y-m-d H:i:s'));
    }
}