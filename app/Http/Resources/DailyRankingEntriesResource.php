<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DailyRankingEntriesResource extends JsonResource {
    /**
     * Transform a single daily ranking entry into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {        
        return [
            'steamid' => (string)$this->steamid,
            'rank' => (int)$this->rank,
            'first_place_ranks' => (int)$this->first_place_ranks,
            'top_5_ranks' => (int)$this->top_5_ranks,
            'top_10_ranks' => (int)$this->top_10_ranks,
            'top_20_ranks' => (int)$this->top_20_ranks,
            'top_50_ranks' => (int)$this->top_50_ranks,
            'top_100_ranks' => (int)$this->top_100_ranks,
            'total_points' => (int)$this->total_points,
            'points_per_day' => $this->total_points / $this->total_dailies,
            'total_score' => (int)$this->total_score,
            'score_per_day' => $this->total_score / $this->total_dailies,
            'total_dailies' => (int)$this->total_dailies,
            'total_wins' => (int)$this->total_wins,
            'sum_of_ranks' => (int)$this->sum_of_ranks,
            'average_rank' => $this->sum_of_ranks / $this->total_dailies
        ];
    }
}
