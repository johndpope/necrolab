<?php
namespace App\Components;

use Exception;

class CallbackHandler {    
    protected $callback;
    
    protected $callback_arguments = array();
    
    protected $callback_reattempts = 1;
    
    protected $callback_reattempt_interval = 1;
    
    protected $success_callback;
    
    protected $success_callback_arguments = array();
    
    public function setCallback($callback) {
        if(!is_callable($callback)) {
            throw new Exception("Specified callback is not valid.");
        }
        
        $this->callback = $callback;
    }
    
    public function setCallbackArguments(array $callback_arguments = array()) {
        $this->callback_arguments = $callback_arguments;
    }
    
    public function getCallbackArguments() {
        return $this->callback_arguments;
    }
    
    public function setCallbackReattempts($callback_reattempts) {
        $this->callback_reattempts = $callback_reattempts;
    }
    
    public function setCallbackReattemptInterval($callback_reattempt_interval) {
        $this->callback_reattempt_interval = $callback_reattempt_interval;
    }
    
    public function setSuccessCallback($callback, array $callback_arguments = array()) {
        if(!is_callable($callback)) {
            throw new Exception("Specified success callback is not valid.");
        }
        
        $this->success_callback = $callback;
        $this->success_callback_arguments = $callback_arguments;
    }
    
    public function execute() {
        if(empty($this->callback)) {
            throw new Exception("Callback has not been set.");
        }
        
        $callback_attempts = 1;
        $callback_successful = false;
        
        $response = NULL;
        
        while($callback_successful == false && $callback_attempts <= $this->callback_reattempts) {
            try {            
                $response = call_user_func_array($this->callback, $this->callback_arguments);

                $callback_successful = true;
            }
            catch(Exception $exception) {
                $callback_successful = false;
                $callback_attempts += 1;
            
                if($callback_attempts <= $this->callback_reattempts) {                    
                    echo "Callback has failed. Making attempt {$callback_attempts} of {$this->callback_reattempts} after waiting {$this->callback_reattempt_interval} seconds.\n";
                
                    sleep($this->callback_reattempt_interval);
                }
                else {
                    throw $exception;
                }
            }
        }
        
        if($callback_successful && !empty($this->success_callback)) {
            $success_callback_arguments = $this->success_callback_arguments;
            
            array_unshift($success_callback_arguments, $response);
            
            call_user_func_array($this->success_callback, $success_callback_arguments);
        }
        
        return $response;
    }
} 
