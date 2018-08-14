<?php

namespace App\Components\Dataset\Traits;

trait HasExternalSiteId {
    protected $external_site_id = 0;
    
    public function setExternalSiteId(int $external_site_id) {
        $this->external_site_id = $external_site_id;
    }
    
    public function getExternalSiteId() {
        return $this->external_site_id;
    }
}
