<?php

namespace App\Http\Resources\Client\Payment;

use Illuminate\Http\Resources\Json\JsonResource;

class Stripe extends JsonResource
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
            'type' => 'stripe',
            'plan' => $this->plan,
            'status' => $this->hand_status, //from mutator
            'start' => optional($this->period_start)->toDateTimeString(),
            'next' => optional($this->period_end)->toDateTimeString(),
            'payments' => StripePayment::collection($this->whenLoaded('payments')),
        ];
    }
}
