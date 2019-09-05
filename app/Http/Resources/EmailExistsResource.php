<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EmailExistsResource extends JsonResource {
    /**
     * Formats the response indicating whether the email exists or not.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {
        return [
            'exists' => $this->resource,
            'state' => $this->resource ? 'good' : 'bad'
        ];
    }
}
