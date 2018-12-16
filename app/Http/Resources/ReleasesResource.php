<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReleasesResource extends JsonResource {
    /**
     * Transforma single release into an array.
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
            'id' => (int)$this->release_id,
            'name' => $this->name,
            'display_name' => $this->display_name,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'modes' => $modes,
            'characters' => $characters
        ];
    }
}
