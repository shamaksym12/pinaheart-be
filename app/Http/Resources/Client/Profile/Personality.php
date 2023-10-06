<?php

namespace App\Http\Resources\Client\Profile;

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
            'desc' => optional($this)->desc,
            'travel' => optional($this)->travel,
            'weekend' => optional($this)->weekend,
            'humor' => optional($this)->humor,
            'person' => optional($this)->person,
            'dress' => optional($this)->dress,
        ];
    }
}
