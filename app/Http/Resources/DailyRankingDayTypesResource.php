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
            'id' => (int)$this->daily_ranking_day_type_id,
            'name' => (string)$this->name,
            'display_name' => $this->display_name,
            $this->mergeWhen(!empty($authenticated_user) && $authenticated_user->hasAnyPermission([
                'permission:daily_ranking_day_types:store',
                'permission:daily_ranking_day_types:update'
            ]), [
                'enabled' => $this->enabled,
            ])
        ];
    }
}
