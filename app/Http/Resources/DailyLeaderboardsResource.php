<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DailyLeaderboardsResource extends JsonResource {
    /**
     * Transform a single release into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {
        $record = [
            'date' => $this->daily_date,
        ];
        
        if(isset($this->players)) {
            $record['players'] = $this->players;
        }
        
        $details = $this->details;
        
        if(empty($details)) {
            $details = [];
        }
        
        $record['details'] = $details;
        
        return $record;
    }
}
