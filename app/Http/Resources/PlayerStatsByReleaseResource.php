<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PlayerStatsByReleaseResource extends JsonResource {
    /**
     * Transforma single release into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {
        $details_decoded = json_decode($this->details, true);
        $details = [];

        if(!empty($details_decoded)) {
            foreach($details_decoded as $details_name => $details_value) {
                if(is_float($details_value + 0)) {
                    $details[$details_name] = (float)$details_value;
                }
                else {
                    $details[$details_name] = (int)$details_value;
                }
            }
        }

        return [
            'date' => $this->date,
            'pbs' => (int)$this->pbs,
            'leaderboards' => (int)$this->leaderboards,
            'first_place_ranks' => (int)$this->first_place_ranks,
            'dailies' => (int)$this->dailies,
            'leaderboard_types' => json_decode($this->leaderboard_types, true),
            'characters' => json_decode($this->characters, true),
            'modes' => json_decode($this->modes, true),
            'seeded_types' => json_decode($this->seeded_types, true),
            'multiplayer_types' => json_decode($this->multiplayer_types, true),
            'soundtracks' => json_decode($this->soundtracks, true),
            'details' => $details
        ];
    }
}
