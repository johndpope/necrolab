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
            'players' => $this->players,
            'first_place_ranks' => $this->first_place_ranks,
            'top_5_ranks' => $this->top_5_ranks,
            'top_10_ranks' => $this->top_10_ranks,
            'top_20_ranks' => $this->top_20_ranks,
            'top_50_ranks' => $this->top_50_ranks,
            'top_100_ranks' => $this->top_100_ranks,
            'total_points' => (float)$this->total_points,
            'total_dailies' => $this->total_dailies,
            'total_wins' => $this->total_wins,
            'sum_of_ranks' => $this->sum_of_ranks,
            'total_score' => $this->total_score
        ];
    }
}
