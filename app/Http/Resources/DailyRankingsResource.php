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
        return [
            'date' => $this->date,
            'players' => (int)$this->players,
            'total_dailies' => (int)$this->total_dailies,
            'total_wins' => (int)$this->total_wins,
            'total_score' => (int)$this->total_score
        ];
    }
}
