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
        $modes = [];
        
        if(!empty($this->modes)) {
            $modes = explode(',', $this->modes);
        }
    
        $characters = [];
        
        if(!empty($this->characters)) {
            $characters = explode(',', $this->characters);
        }
    
        return [
            'id' => (int)$this->id,
            'name' => $this->name,
            'display_name' => $this->display_name,
            'details_column_name' => $this->details_column,
            'modes' => $modes,
            'characters' => $characters
        ];
    }
}
