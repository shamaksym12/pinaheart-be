<?php

namespace App\Http\Resources\Client\Person;

use Illuminate\Http\Resources\Json\JsonResource;

class Info extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'heading' => optional($this)->heading,
            'about' => optional($this)->about,
            'looking' => optional($this)->looking,
        ];
    }
}
