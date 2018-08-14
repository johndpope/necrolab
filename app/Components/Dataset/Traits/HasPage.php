<?php

namespace App\Components\Dataset\Traits;

trait HasPage {
    protected $page = 1;
    
    public function setPage(int $page) {
        $this->page = $page;
    }
    
    public function getPage() {
        return $this->page;
    }
}
