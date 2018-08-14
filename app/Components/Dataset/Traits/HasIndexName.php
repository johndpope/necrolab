<?php

namespace App\Components\Dataset\Traits;

trait HasIndexName {
    protected $index_name = '';
    
    public function setIndexName(string $index_name) {
        $this->index_name = $index_name;
    }
    
    public function getIndexName() {
        return $this->index_name;
    }
}
