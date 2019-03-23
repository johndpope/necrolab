<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DailyRankingDayTypesResource extends JsonResource {
    /**
     * Transform a single daily ranking day type into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {
        $authenticated_user = $request->user();
        
        return [
            'id' => (int)$this->id,
            'name' => (string)$this->name,
            'display_name' => $this->display_name,
            'is_default' => $this->is_default
        ];
    }
}
