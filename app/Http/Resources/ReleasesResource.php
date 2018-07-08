<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReleasesResource extends JsonResource {
    /**
     * Transforma single release into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {
        return [
            'id' => (int)$this->release_id,
            'name' => $this->name,
            'display_name' => $this->display_name,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date
        ];
    }
}
