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
        
        if(!empty($this->external_id)) {            
            $rankings = [];
            
            if(!empty($this->ranking_types)) {
                $rankings = explode(',', $this->ranking_types);
            }
        
            $record = [
                'id' => $this->external_id,
                'name' => $this->name,
                'display_name' => $this->display_name,
                'rankings' => $rankings,
                'leaderboard_type' => $this->leaderboard_type,
                'character' => $this->character,
                'release' => $this->release,
                'mode' => $this->mode,
                'seeded_type' => $this->seeded_type,
                'multiplayer_type' => $this->multiplayer_type,
                'soundtrack' => $this->soundtrack,
                'show_seed' => $this->show_seed,
                'show_replay' => $this->show_replay,
                'show_zone_level' => $this->show_zone_level
            ];
        }
    
        return $record;
    }
}
