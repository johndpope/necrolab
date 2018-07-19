<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SteamUsersResource extends JsonResource {
    /**
     * Transforma single release into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {
        return [
            'steamid' => $this->steamid,
            'personaname' => $this->personaname,
            'profileurl' => $this->profileurl
        ];
    }
}
