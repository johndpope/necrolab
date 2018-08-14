<?php

namespace App\Components\Dataset\Traits;

trait HasSearchTerm {
    protected $search_term = '';
    
    public function setSearchTerm(string $search_term) {
        if(empty($search_term)) {
            $search_term = '';
        }
    
        $this->search_term = $search_term;
    }
    
    public function getSearchTerm() {
        return $this->search_term;
    }
}
