<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LeaderboardDetailsColumnsResource extends JsonResource {
    /**
     * Transform a single leaderboard details column record into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {    
        return [
            'id' => (int)$this->id,
            'name' => $this->name,
            'display_name' => $this->display_name,
            'data_type' => $this->data_type
        ];
    }
}
