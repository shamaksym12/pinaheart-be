<?php

namespace App\Http\Resources\Admin\User;

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
            'fun' => optional($this)->fun,
            'music' => optional($this)->music,
            'food' => optional($this)->food,
            'sport' => optional($this)->sport,
        ];
    }
}
