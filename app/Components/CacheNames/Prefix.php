<?php

namespace App\Components\CacheNames;

class Prefix {    
    protected $segments = [];
    
    public function __construct(array $segments = []) {
        if(!empty($segments)) {
            foreach($segments as $name => $value) {
                $this->segments[$name] = $value;
            }
        }
    }
    
    public function __set($segment_name, $value) {
        $this->segments[$segment_name] = $value;
    }
    
    public function __get($segment_name) {
        return $this->segments[$segment_name] ?? NULL;
    }
    
    public function __unset($segment_name) {
        if(isset($this->segments[$segment_name])) {
            unset($this->segments[$segment_name]);
        }
    }
    
    public function __toString(): string {
        return implode(':', $this->segments);
    }
}
