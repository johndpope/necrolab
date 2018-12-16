<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LeaderboardSourcesResource extends JsonResource {
    /**
     * Transform a single release into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {
        $releases = [];
        
        if(!empty($this->releases)) {
            $releases = explode(',', $this->releases);
        }
    
        $characters = [];
        
        if(!empty($this->characters)) {
            $characters = explode(',', $this->characters);
        }
    
        return [
            'id' => (int)$this->id,
            'name' => $this->name,
            'url_name' => $this->url_name,
            'display_name' => $this->display_name,
            'releases' => $releases,
            'characters' => $characters
        ];
    }
}
