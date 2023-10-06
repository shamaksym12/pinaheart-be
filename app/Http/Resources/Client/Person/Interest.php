<?php

namespace App\Http\Resources\Client\Person;

use Illuminate\Http\Resources\Json\JsonResource;

class Interest extends JsonResource
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
            'interest_fun' => optional($this)->fun,
            'interest_music' => optional($this)->music,
            'interest_food' => optional($this)->food,
            'interest_sport' => optional($this)->sport,
        ];
    }
}
