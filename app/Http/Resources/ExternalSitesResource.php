<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExternalSitesResource extends JsonResource {
    /**
     * Transform a single external site into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {        
        $authenticated_user = $request->user();
    
        return [
            'id' => (int)$this->external_site_id,
            'name' => $this->name,
            'display_name' => $this->display_name,
            $this->mergeWhen(!empty($authenticated_user) && $authenticated_user->hasAnyPermission([
                'permission:external_sites:store',
                'permission:external_sites:update'
            ]), [
                'active' => (int)$this->active
            ])
        ];
    }
}
