<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\RankPoints;

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
            $record['player'] = new PlayersResource($this->resource);
        }

        $rank = (int)$this->rank;

        $details = [];

        if(!empty($this->details)) {
            foreach($this->details as $details_name => $details_value) {
                if(is_float($details_value + 0)) {
                    $details[$details_name] = (float)$details_value;
                }
                else {
                    $details[$details_name] = (int)$details_value;
                }
            }
        }

        $record['rank'] = $rank;
        $record['points'] = RankPoints::calculateFromRank($rank);
        $record['first_place_ranks'] = (int)$this->first_place_ranks;
        $record['top_5_ranks'] = (int)$this->top_5_ranks;
        $record['top_10_ranks'] = (int)$this->top_10_ranks;
        $record['top_20_ranks'] = (int)$this->top_20_ranks;
        $record['top_50_ranks'] = (int)$this->top_50_ranks;
        $record['top_100_ranks'] = (int)$this->top_100_ranks;
        $record['dailies'] = (int)$this->dailies;
        $record['wins'] = (int)$this->wins;
        $record['sum_of_ranks'] = (int)$this->sum_of_ranks;
        $record['average_rank'] = $this->sum_of_ranks / $this->dailies;
        $record['details'] = $details;

        return $record;
    }
}
