<?php

namespace App\Components\Dataset\Traits;

trait HasIndexSubName {
    protected $index_sub_name = '';
    
    public function setIndexSubName(string $index_sub_name) {
        $this->index_sub_name = $index_sub_name;
    }
    
    public function getIndexSubName() {
        return $this->index_sub_name;
    }
}
