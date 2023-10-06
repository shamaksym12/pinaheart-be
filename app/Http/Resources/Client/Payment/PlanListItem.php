<?php

namespace App\Http\Resources\Client\Payment;

use Illuminate\Http\Resources\Json\JsonResource;

class PlanListItem extends JsonResource
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
            'id' => $this->id,
            'type' => $this->type,
            'origin_id' => $this->origin_id,
            'name' => $this->name,
            'unit' => $this->unit,
            'unit_count' => $this->unit_count,
            'price' => $this->price,
        ];
    }
}
