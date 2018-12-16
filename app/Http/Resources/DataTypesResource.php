<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DataTypesResource extends JsonResource {
    /**
     * Transform a single data type into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {    
        return [
            'id' => (int)$this->id,
            'name' => $this->name,
            'display_name' => $this->display_name
        ];
    }
}
