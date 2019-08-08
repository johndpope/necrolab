<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PlayerStatsResource extends JsonResource {
    /**
     * Transforma single release into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {
        $bests = [
            'leaderboard_type' => $this->best_leaderboard_type,
            'character' => $this->best_character,
            'release' => $this->best_release,
            'mode' => $this->best_mode,
            'seeded_type' => $this->best_seeded_type,
            'multiplayer_type' => $this->best_multiplayer_type,
            'soundtrack' => $this->best_soundtrack,
        ];

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
            'seeded_pbs' => (int)$this->seeded_pbs,
            'unseeded_pbs' => (int)$this->unseeded_pbs,
            'bests' => $bests,
            'details' => $details
        ];
    }
}
