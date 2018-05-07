<?php
namespace App\Components;

use Exception;

class CallbackHandler {    
    protected $callback;
    
    protected $arguments = [];
    
    protected $reattempts = 1;
    
    protected $reattempt_interval = 1;
    
    protected $success_callback;
    
    protected $success_arguments = [];
    
    public function setCallback(callable $callback) {
        if(!is_callable($callback)) {
            throw new Exception("Specified callback is not valid.");
        }
        
        $this->callback = $callback;
    }
    
    public function setArguments(array $arguments = []) {
        $this->arguments = $arguments;
    }
    
    public function addArgument($argument) {
        $this->arguments[] = $argument;
    }
    
    public function prependArgument($argument) {
        array_unshift($this->arguments, $argument);
    }
    
    public function removeFirstArgument() {
        array_shift($this->arguments);
    }
    
    public function removeLastArgument() {
        array_pop($this->arguments);
    }
    
    public function getArguments() {
        return $this->arguments;
    }
    
    public function setReattempts(int $reattempts) {
        $this->reattempts = $reattempts;
    }
    
    public function setReattemptInterval(int $reattempt_interval) {
        $this->reattempt_interval = $reattempt_interval;
    }
    
    public function setSuccessCallback(callable $callback, array $arguments = []) {
        if(!is_callable($callback)) {
            throw new Exception("Specified success callback is not valid.");
        }
        
        $this->success_callback = $callback;
        $this->success_arguments = $arguments;
    }
    
    public function execute() {
        if(empty($this->callback)) {
            throw new Exception("Callback has not been set.");
        }
        
        $callback_attempts = 1;
        $callback_successful = false;
        
        $response = NULL;
        
        while($callback_successful == false && $callback_attempts <= $this->reattempts) {
            try {            
                $response = call_user_func_array($this->callback, $this->arguments);

                $callback_successful = true;
            }
            catch(Exception $exception) {
                $callback_successful = false;
                $callback_attempts += 1;
            
                if($callback_attempts <= $this->reattempts) {                    
                    echo "Callback has failed. Making attempt {$callback_attempts} of {$this->reattempts} after waiting {$this->reattempt_interval} seconds.\n";
                
                    sleep($this->reattempt_interval);
                }
                else {
                    throw $exception;
                }
            }
        }
        
        if($callback_successful && !empty($this->success_callback)) {
            $success_arguments = $this->success_arguments;
            
            array_unshift($success_arguments, $response);
            
            call_user_func_array($this->success_callback, $success_arguments);
        }
        
        return $response;
    }
} 
