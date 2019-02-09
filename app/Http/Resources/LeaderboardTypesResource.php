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
        return [
            'id' => (int)$this->id,
            'name' => $this->name,
            'display_name' => $this->display_name,
            'is_default' => $this->is_default,
            'details_columns' => $this->details_columns,
            'modes' => $this->modes,
            'characters' => $this->characters
        ];
    }
}
