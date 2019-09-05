<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PasswordValidResource extends JsonResource {
    /**
     * Formats the response indicating whether the email exists or not.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {
        return [
            'state' => !empty($this->resource) ? 'good' : 'bad'
        ];
    }
}
