<?php
namespace App\Components;

use DateTime;
use DateInterval;
use App\Components\CallbackHandler;

class DateIncrementor {
    protected $start_date;
    
    protected $end_date;
    
    protected $interval;
    
    protected $callback_handler;
    
    public function __construct(DateTime $start_date, DateTime $end_date, DateInterval $interval) {
        $this->start_date = $start_date;
        
        $this->end_date = $end_date;
        
        $this->interval = $interval;
    }
    
    public function run(CallbackHandler $callback_handler) {
        $current_date = clone $this->start_date;
        
        while($current_date <= $this->end_date) {
            $callback_handler->setCallbackArguments([
                $current_date
            ]);
            
            $callback_handler->execute();
        
            $current_date->add($this->interval);
        }
    }
}