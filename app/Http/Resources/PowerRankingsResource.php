<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PowerRankingsResource extends JsonResource {
    /**
     * Transform a single release into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {    
        return $this->date;
    }
}
