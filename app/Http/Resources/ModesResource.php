<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ModesResource extends JsonResource {
    /**
     * Transform a single mode into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {        
        $authenticated_user = $request->user();
    
        return [
            'id' => (int)$this->mode_id,
            'name' => $this->name,
            'display_name' => $this->display_name,
            $this->mergeWhen(!empty($authenticated_user) && $authenticated_user->hasAnyPermission([
                'permission:modes:store',
                'permission:modes:update'
            ]), [
                'sort_order' => $this->sort_order
            ])
        ];
    }
}
