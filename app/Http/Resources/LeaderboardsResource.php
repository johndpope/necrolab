<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LeaderboardsResource extends JsonResource {
    /**
     * Transform a single release into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {    
        $record = [];
        
        if(!empty($this->leaderboard_id)) {            
            $rankings = [];
            
            if(!empty($this->rankings)) {
                $rankings = explode(',', $this->rankings);
            }
        
            $record = [
                'id' => $this->lbid,
                'name' => $this->name,
                'display_name' => $this->display_name,
                'url_name' => $this->url_name,
                'character' => $this->character_name,
                'type' => $this->leaderboard_type_name,
                'rankings' => $rankings,
                'seeded_type' => $this->seeded_type,
                'multiplayer_type' => $this->multiplayer_type,
                'soundtrack' => $this->soundtrack
            ];
        }
    
        return $record;
    }
}
