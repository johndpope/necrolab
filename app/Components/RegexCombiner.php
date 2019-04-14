<?php
namespace App\Components;

/*
 * This class is designed to combine multiple regex statements into one for a significant performance improvement.
 * Further reading: 
 * - https://medium.com/@nicolas.grekas/making-symfonys-router-77-7x-faster-1-2-958e3754f0e1
 * - https://stackoverflow.com/questions/16195011/combine-multiple-match-regular-expression-into-one-and-get-the-matching-ones
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
        $this->segments[$name] = "(?P<group_{$name}>{$segment}(*MARK:{$name}))";
    }
    
    public function addSegments(array $segments): void {
        if(!empty($segments)) {
            foreach($segments as $name => $segment) {
                $this->addSegment($name, $segment);
            }
        }
    }
    
    public function getCombined(): string {
        return '@' . implode("|", $this->segments) . "@im";
    }
}
