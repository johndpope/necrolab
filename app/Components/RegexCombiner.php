<?php
namespace App\Components;

/*
 * This class is designed to combine multiple regex statements into one for a significant performance improvement.
 * Further reading: https://medium.com/@nicolas.grekas/making-symfonys-router-77-7x-faster-1-2-958e3754f0e1
 */
class RegexCombiner {  
    protected $segments = [];
    
    public function __construct(array $segments = array()) {        
        $this->setSegments($segments);
    }
    
    public function setSegments(array $segments): void {
        $this->segments = [];
        
        $this->addSegments($segments);
    }
    
    public function addSegment(string $name, string $segment): void {
        $this->segments[$name] = "{$segment} (*MARK:{$name})";
    }
    
    public function addSegments(array $segments): void {
        if(!empty($segments)) {
            foreach($segments as $name => $segment) {
                $this->addSegment($name, $segment);
            }
        }
    }
    
    public function getCombined(): string {
        return '/^.*(?|' . implode("\n|", $this->segments) . "\n).*$/ix";
    }
}
