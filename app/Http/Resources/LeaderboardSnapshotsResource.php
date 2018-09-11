<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LeaderboardSnapshotsResource extends JsonResource {
    /**
     * Transform a single release into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {    
        return [
            'date' => $this->date,
            'players' => $this->players,
            'score' => $this->score,
            'time' => $this->time,
            'win_count' => $this->win_count
        ];
    }
}
