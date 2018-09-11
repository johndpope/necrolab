<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LeaderboardEntriesResource extends JsonResource {
    /**
     * Transform a single release into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {
        $record = [
            'rank' => $this->rank
        ];
        
        if(!empty($this->steamid)) {
            $record['player'] = new SteamUsersResource($this->resource);
        }
        
        $record['pb'] = new SteamUserPbsResource($this->resource);
    
        return $record;
    }
}
