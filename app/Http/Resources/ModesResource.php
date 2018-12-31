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
        $leaderboard_types = [];
        
        if(!empty($this->leaderboard_types)) {
            $leaderboard_types = explode(',', $this->leaderboard_types);
        }
    
        $characters = [];
        
        if(!empty($this->characters)) {
            $characters = explode(',', $this->characters);
        }
    
        return [
            'id' => (int)$this->id,
            'name' => $this->name,
            'display_name' => $this->display_name,
            'leaderboard_types' => $leaderboard_types,
            'characters' => $characters
        ];
    }
}
