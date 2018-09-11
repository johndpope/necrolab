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
        $record = [];
        
        // If this record is in a Steam User context then only show its date. Otherwise show player data.
        if(!empty($this->date)) {
            $record['date'] = $this->date;
        }
        else {
            $record['player'] = new SteamUsersResource($this->resource);
        }
        
        $record['rank'] = (int)$this->rank;
        $record['first_place_ranks'] = (int)$this->first_place_ranks;
        $record['top_5_ranks'] = (int)$this->top_5_ranks;
        $record['top_10_ranks'] = (int)$this->top_10_ranks;
        $record['top_20_ranks'] = (int)$this->top_20_ranks;
        $record['top_50_ranks'] = (int)$this->top_50_ranks;
        $record['top_100_ranks'] = (int)$this->top_100_ranks;
        $record['total_points'] = (float)$this->total_points;
        $record['points_per_day'] = $this->total_points / $this->total_dailies;
        $record['total_score'] = (int)$this->total_score;
        $record['score_per_day'] = $this->total_score / $this->total_dailies;
        $record['total_dailies'] = (int)$this->total_dailies;
        $record['total_wins'] = (int)$this->total_wins;
        $record['sum_of_ranks'] = (int)$this->sum_of_ranks;
        $record['average_rank'] = $this->sum_of_ranks / $this->total_dailies;
        
        return $record;
    }
}
