<?php

namespace App\Http\Resources\Client\Person;

use Illuminate\Http\Resources\Json\JsonResource;

class Personality extends JsonResource
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
            'personality_desc' => optional($this)->desc,
            'personality_travel' => optional($this)->travel,
            'personality_weekend' => optional($this)->weekend,
            'personality_humor' => optional($this)->humor,
            'personality_person' => optional($this)->person,
            'personality_dress' => optional($this)->dress,
        ];
    }
}
