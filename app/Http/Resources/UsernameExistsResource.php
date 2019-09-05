<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UsernameExistsResource extends JsonResource {
    /**
     * Formats the response indicating whether the username exists or not.
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
