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
            'id' => (int)$this->id,
            'name' => $this->name,
            'display_name' => $this->display_name
        ];
    }
}
