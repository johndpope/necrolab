<?php

namespace App\Http\Resources;

use stdClass;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Components\Encoder;

class PowerRankingsResource extends JsonResource {
    /**
     * Transform a single release into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {
        $record = [
            'date' => $this->date,
            'players' => $this->players
        ];
        
        if(!empty($this->categories)) {
            foreach($this->categories as $category_name => $category) {
                $record[$category_name] = $category;
            }
        }
        
        if(!empty($this->characters)) {
            $record['characters'] = $this->characters;
        }
        else {
            $record['characters'] = new stdClass();
        }
    
        return $record;
    }
}
