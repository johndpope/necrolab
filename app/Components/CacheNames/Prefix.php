<?php

namespace App\Components\CacheNames;

class Prefix {    
    protected $segments = [];
    
    public function __set($segment_name, $value) {
        $this->segments[$segment_name] = $value;
    }
    
    public function __get($segment_name) {
        return $this->segments[$segment_name] ?? NULL;
    }
    
    public function __toString(): string {
        return implode(':', $this->segments);
    }
}
