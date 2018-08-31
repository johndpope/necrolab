<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LeaderboardTypesResource extends JsonResource {
    /**
     * Transform a single mode into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {        
        $authenticated_user = $request->user();
    
        return [
            'id' => (int)$this->leaderboard_type_id,
            'name' => $this->name,
            'display_name' => $this->display_name
        ];
    }
}
