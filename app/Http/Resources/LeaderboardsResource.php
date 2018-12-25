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
            
            if(!empty($this->ranking_types)) {
                $rankings = explode(',', $this->ranking_types);
            }
        
            $record = [
                'id' => $this->lbid,
                'name' => $this->name,
                'display_name' => $this->display_name,
                'rankings' => $rankings,
                //TODO: make this dynamic when leaderboards are linked to sources
                'leaderboard_source' => 'steam',
                'leaderboard_type' => $this->leaderboard_type,
                'release' => $this->release,
                'mode' => $this->mode,
                'character' => $this->character,
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
