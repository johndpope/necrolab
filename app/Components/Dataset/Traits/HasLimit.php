<?php

namespace App\Components\Dataset\Traits;

trait HasLimit {
    protected $limit = 100;
    
    public function setLimit(int $limit) {
        $this->limit = $limit;
    }
    
    public function getLimit() {
        return $this->limit;
    }
}
