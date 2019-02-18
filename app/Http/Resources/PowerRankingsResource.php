<?php

namespace App\Http\Resources;

use stdClass;
use Illuminate\Http\Resources\Json\JsonResource;

class PowerRankingsResource extends JsonResource {
    /**
     * Transform a single release into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {
        $categories = $this->categories;
        
        if(empty($categories)) {
            $categories = [];
        }
        
        $characters = $this->characters;
        
        if(empty($characters)) {
            $characters = [];
        }
    
        return [
            'date' => $this->date,
            'players' => $this->players,
            'categories' => $categories,
            'characters' => $characters
        ];
    }
}
