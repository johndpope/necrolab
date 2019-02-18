<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DailyRankingsResource extends JsonResource {
    /**
     * Transform a single release into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {
        $details = $this->details;
        
        if(empty($details)) {
            $details = [];
        }
    
        return [
            'date' => $this->date,
            'players' => $this->players,
            'dailies' => $this->dailies,
            'wins' => $this->wins,
            'details' => $details
        ];
    }
}
