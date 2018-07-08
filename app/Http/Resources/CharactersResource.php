<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CharactersResource extends JsonResource {
    /**
     * Transform a single character into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {
        $authenticated_user = $request->user();
        
        return [
            'id' => (int)$this->character_id,
            'name' => $this->name,
            'display_name' => $this->display_name,
            $this->mergeWhen(!empty($authenticated_user) && $authenticated_user->hasAnyPermission([
                'permission:characters:store',
                'permission:characters:update'
            ]), [
                'is_active' => $this->is_active,
                'steam_match' => $this->steam_match,
                'sort_order' => $this->sort_order
            ])
        ];
    }
}
