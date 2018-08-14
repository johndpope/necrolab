<?php

namespace App\Components\Dataset\Traits;

trait HasIndexFieldName {
    protected $index_field_name = '';
    
    public function setIndexFieldName(string $index_field_name) {
        $this->index_field_name = $index_field_name;
    }
    
    public function getIndexFieldName() {
        return $this->index_field_name;
    }
}
